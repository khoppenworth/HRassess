<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../i18n.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') { csrf_check(); }

if (($_POST['action'] ?? '')==='create_q') {
  $stmt = $pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?, ?)");
  $stmt->execute([$_POST['title'], $_POST['description']]);
  log_action($pdo, (int)$_SESSION['user_id'], 'create_questionnaire', ['title'=>$_POST['title']]);
  $msg = 'Questionnaire created.';
}

if (($_POST['action'] ?? '')==='add_section') {
  $stmt = $pdo->prepare("INSERT INTO questionnaire_section (questionnaire_id, title, description, order_index) VALUES (?,?,?,?)");
  $stmt->execute([(int)$_POST['questionnaire_id'], $_POST['section_title'], $_POST['section_desc'] ?? null, (int)($_POST['order_index'] ?? 0)]);
  $msg = 'Section added.';
}

if (($_POST['action'] ?? '')==='update_section') {
  $stmt = $pdo->prepare("UPDATE questionnaire_section SET title=?, description=?, order_index=? WHERE id=?");
  $stmt->execute([$_POST['section_title'], $_POST['section_desc'] ?? null, (int)$_POST['order_index'], (int)$_POST['section_id']]);
  $msg = 'Section updated.';
}

if (isset($_GET['delete_section'])) {
  $sid = (int)$_GET['delete_section'];
  $pdo->prepare("DELETE FROM questionnaire_section WHERE id=?")->execute([$sid]);
  $msg = 'Section deleted.';
}

if (($_POST['action'] ?? '')==='add_item') {
  $stmt = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, section_id, linkId, text, type, order_index) VALUES (?,?,?,?,?,?)");
  $stmt->execute([(int)$_POST['questionnaire_id'], ($_POST['section_id'] ?: null), ($_POST['linkId'] ?: uniqid('q')), $_POST['text'], $_POST['type'], (int)($_POST['order_index'] ?? 0)]);
  $msg = 'Item added.';
}

if (($_POST['action'] ?? '')==='update_item') {
  $stmt = $pdo->prepare("UPDATE questionnaire_item SET section_id=?, linkId=?, text=?, type=?, order_index=? WHERE id=?");
  $sid = $_POST['section_id'] !== '' ? (int)$_POST['section_id'] : null;
  $stmt->execute([$sid, $_POST['linkId'], $_POST['text'], $_POST['type'], (int)$_POST['order_index'], (int)$_POST['item_id']]);
  $msg = 'Item updated.';
}

if (isset($_GET['delete_item'])) {
  $iid = (int)$_GET['delete_item'];
  $pdo->prepare("DELETE FROM questionnaire_item WHERE id=?")->execute([$iid]);
  $msg = 'Item deleted.';
}

if (($_POST['action'] ?? '')==='import_q' && !empty($_FILES['qfile']['tmp_name'])) {
  $content = file_get_contents($_FILES['qfile']['tmp_name']);
  $ext = strtolower(pathinfo($_FILES['qfile']['name'], PATHINFO_EXTENSION));
  try {
    if ($ext === 'json') {
      $data = json_decode($content, true);
      if (!is_array($data) || ($data['resourceType'] ?? '') !== 'Questionnaire') {
        throw new Exception('Invalid FHIR Questionnaire JSON.');
      }
      $stmt = $pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?, ?)");
      $stmt->execute([$data['title'] ?? 'Imported Questionnaire', $data['description'] ?? '']);
      $qid = $pdo->lastInsertId();
      $ins = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, section_id, linkId, text, type, order_index) VALUES (?,?,?,?,?,?)");
      $ord = 1;
      foreach ($data['item'] ?? [] as $it) {
        $ins->execute([$qid, null, $it['linkId'] ?? uniqid('q'), $it['text'] ?? '', $it['type'] ?? 'text', $ord++]);
      }
      $msg = 'Questionnaire imported (JSON).';
    } elseif ($ext === 'xml') {
      $xml = @simplexml_load_string($content);
      if ($xml === false) throw new Exception('Invalid XML.');
      $title = (string)($xml->title ?? 'Imported Questionnaire');
      $desc = (string)($xml->description ?? '');
      $stmt = $pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?, ?)");
      $stmt->execute([$title, $desc]);
      $qid = $pdo->lastInsertId();
      $ins = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, section_id, linkId, text, type, order_index) VALUES (?,?,?,?,?,?)");
      $ord = 1;
      foreach ($xml->item as $it) {
        $linkId = (string)($it['linkId'] ?? uniqid('q'));
        $text = (string)($it->text ?? '');
        $type = (string)($it['type'] ?? 'text');
        $ins->execute([$qid, null, $linkId, $text, $type, $ord++]);
      }
      $msg = 'Questionnaire imported (XML).';
    } else {
      throw new Exception('Unsupported file type.');
    }
  } catch (Exception $e) {
    $msg = 'Import failed: ' . $e->getMessage();
  }
}

if (isset($_GET['download_template'])) {
  header('Content-Type: application/xml');
  header('Content-Disposition: attachment; filename="questionnaire_template.xml"');
  readfile(__DIR__ . '/../samples/sample_questionnaire_template.xml');
  exit;
}

$questionnaires = $pdo->query("SELECT * FROM questionnaire ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['manage_questionnaires']) ?></h2>
    <?php if($msg): ?><div class="alert alert-info"><?=$msg?></div><?php endif; ?>

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><?= htmlspecialchars($t['create_questionnaire']) ?></h3>
        <a class="btn btn-sm btn-outline-secondary" href="/admin/questionnaire_manage.php?download_template=1"><i class="fas fa-download"></i> <?= htmlspecialchars($t['download_template']) ?></a>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action" value="create_q">
          <div class="form-group"><label><?= htmlspecialchars($t['title']) ?></label><input class="form-control" name="title" required></div>
          <div class="form-group"><label><?= htmlspecialchars($t['description']) ?></label><textarea class="form-control" name="description"></textarea></div>
          <button class="btn btn-primary"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['add_section']) ?></h3></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action" value="add_section">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label><?= htmlspecialchars($t['questionnaire']) ?></label>
              <select class="form-control" name="questionnaire_id">
                <?php foreach($questionnaires as $q): ?>
                  <option value="<?=$q['id']?>"><?=htmlspecialchars($q['title'])?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-4"><label><?= htmlspecialchars($t['section_title']) ?></label><input class="form-control" name="section_title" required></div>
            <div class="form-group col-md-2"><label><?= htmlspecialchars($t['order']) ?></label><input class="form-control" type="number" name="order_index" value="0"></div>
          </div>
          <div class="form-group"><label><?= htmlspecialchars($t['description']) ?></label><textarea class="form-control" name="section_desc"></textarea></div>
          <button class="btn btn-secondary"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['add_question']) ?></h3></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action" value="add_item">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label><?= htmlspecialchars($t['questionnaire']) ?></label>
              <select class="form-control" name="questionnaire_id">
                <?php foreach($questionnaires as $q): ?>
                  <option value="<?=$q['id']?>"><?=htmlspecialchars($q['title'])?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label><?= htmlspecialchars($t['sections']) ?></label>
              <select class="form-control" name="section_id">
                <option value="">-- none --</option>
                <?php foreach($questionnaires as $q): 
                  $secs = $pdo->prepare("SELECT id,title FROM questionnaire_section WHERE questionnaire_id=? ORDER BY order_index ASC, id ASC");
                  $secs->execute([$q['id']]); foreach($secs as $s): ?>
                  <option value="<?=$s['id']?>"><?=htmlspecialchars($q['title'].' / '.$s['title'])?></option>
                <?php endforeach; endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md-2"><label><?= htmlspecialchars($t['linkId']) ?></label><input class="form-control" name="linkId" placeholder="auto if blank"></div>
            <div class="form-group col-md-2">
              <label><?= htmlspecialchars($t['type']) ?></label>
              <select class="form-control" name="type">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="boolean">Yes/No</option>
              </select>
            </div>
            <div class="form-group col-md-2"><label><?= htmlspecialchars($t['order']) ?></label><input class="form-control" type="number" name="order_index" value="0"></div>
          </div>
          <div class="form-group"><label><?= htmlspecialchars($t['text']) ?></label><input class="form-control" name="text" required></div>
          <button class="btn btn-secondary"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['existing_questionnaires']) ?></h3></div>
      <div class="card-body">
        <?php foreach($questionnaires as $q): ?>
          <div class="mb-4 p-2 border rounded">
            <h5><?=htmlspecialchars($q['title'])?></h5>
            <?php
              $secs = $pdo->prepare("SELECT * FROM questionnaire_section WHERE questionnaire_id=? ORDER BY order_index ASC, id ASC");
              $secs->execute([$q['id']]); $secRows = $secs->fetchAll();
              $its = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id=? ORDER BY order_index ASC, id ASC");
              $its->execute([$q['id']]); $items = $its->fetchAll();
              $itemsBySec = [];
              foreach($items as $it){ $itemsBySec[$it['section_id']??0][]=$it; }
            ?>

            <h6 class="text-muted mt-2"><?= htmlspecialchars($t['sections']) ?></h6>
            <?php foreach($secRows as $s): ?>
              <form method="post" class="border rounded p-2 mb-2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="action" value="update_section">
                <input type="hidden" name="section_id" value="<?=$s['id']?>">
                <div class="form-row">
                  <div class="form-group col-md-4"><input class="form-control" name="section_title" value="<?=htmlspecialchars($s['title'])?>"></div>
                  <div class="form-group col-md-6"><input class="form-control" name="section_desc" value="<?=htmlspecialchars($s['description'])?>"></div>
                  <div class="form-group col-md-1"><input class="form-control" type="number" name="order_index" value="<?=$s['order_index']?>"></div>
                  <div class="form-group col-md-1">
                    <button class="btn btn-sm btn-success" title="<?= htmlspecialchars($t['save']) ?>"><i class="fas fa-save"></i></button>
                    <a class="btn btn-sm btn-danger" onclick="return confirm('Delete section?')" href="/admin/questionnaire_manage.php?delete_section=<?=$s['id']?>"><i class="fas fa-trash"></i></a>
                  </div>
                </div>

                <?php foreach($itemsBySec[$s['id']] ?? [] as $it): ?>
                  <div class="form-row mb-1">
                    <form method="post"></form>
                    <form method="post" class="w-100 d-flex align-items-center">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="action" value="update_item">
                      <input type="hidden" name="item_id" value="<?=$it['id']?>">
                      <input type="hidden" name="section_id" value="<?=$s['id']?>">
                      <input class="form-control form-control-sm mr-1" style="max-width:110px" name="linkId" value="<?=htmlspecialchars($it['linkId'])?>">
                      <input class="form-control form-control-sm mr-1" name="text" value="<?=htmlspecialchars($it['text'])?>">
                      <select class="form-control form-control-sm mr-1" name="type">
                        <option value="text" <?= $it['type']==='text'?'selected':'' ?>>Text</option>
                        <option value="textarea" <?= $it['type']==='textarea'?'selected':'' ?>>Textarea</option>
                        <option value="boolean" <?= $it['type']==='boolean'?'selected':'' ?>>Yes/No</option>
                      </select>
                      <input class="form-control form-control-sm mr-1" style="max-width:90px" type="number" name="order_index" value="<?=$it['order_index']?>">
                      <button class="btn btn-sm btn-success mr-1"><i class="fas fa-save"></i></button>
                      <a class="btn btn-sm btn-danger" onclick="return confirm('Delete item?')" href="/admin/questionnaire_manage.php?delete_item=<?=$it['id']?>"><i class="fas fa-trash"></i></a>
                    </form>
                  </div>
                <?php endforeach; ?>
              </form>
            <?php endforeach; ?>

            <?php if (!empty($itemsBySec[0])): ?>
              <div class="mt-2">
                <h6 class="text-muted">General (no section)</h6>
                <?php foreach($itemsBySec[0] as $it): ?>
                  <form method="post" class="d-flex align-items-center mb-1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <input type="hidden" name="action" value="update_item">
                    <input type="hidden" name="item_id" value="<?=$it['id']?>">
                    <input type="hidden" name="section_id" value="">
                    <input class="form-control form-control-sm mr-1" style="max-width:110px" name="linkId" value="<?=htmlspecialchars($it['linkId'])?>">
                    <input class="form-control form-control-sm mr-1" name="text" value="<?=htmlspecialchars($it['text'])?>">
                    <select class="form-control form-control-sm mr-1" name="type">
                      <option value="text" <?= $it['type']==='text'?'selected':'' ?>>Text</option>
                      <option value="textarea" <?= $it['type']==='textarea'?'selected':'' ?>>Textarea</option>
                      <option value="boolean" <?= $it['type']==='boolean'?'selected':'' ?>>Yes/No</option>
                    </select>
                    <input class="form-control form-control-sm mr-1" style="max-width:90px" type="number" name="order_index" value="<?=$it['order_index']?>">
                    <button class="btn btn-sm btn-success mr-1"><i class="fas fa-save"></i></button>
                    <a class="btn btn-sm btn-danger" onclick="return confirm('Delete item?')" href="/admin/questionnaire_manage.php?delete_item=<?=$it['id']?>"><i class="fas fa-trash"></i></a>
                  </form>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['import_questionnaire']) ?></h3></div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action" value="import_q">
          <div class="form-group"><input type="file" name="qfile" accept=".json,.xml" class="form-control" required></div>
          <button class="btn btn-primary"><?= htmlspecialchars($t['import']) ?></button>
        </form>
      </div>
    </div>

  </div>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
