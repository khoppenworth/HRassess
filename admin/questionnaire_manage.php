<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../i18n.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg = '';

// Create questionnaire
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='create_q') {
  $stmt = $pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?, ?)");
  $stmt->execute([$_POST['title'], $_POST['description']]);
  $msg = 'Questionnaire created.';
}

// Add question
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='add_item') {
  $stmt = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, linkId, text, type) VALUES (?, ?, ?, ?)");
  $stmt->execute([ (int)$_POST['questionnaire_id'], $_POST['linkId'] ?: uniqid('q'), $_POST['text'], $_POST['type'] ]);
  $msg = 'Item added.';
}

// Import questionnaire (FHIR JSON or XML)
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='import_q') {
  if (!empty($_FILES['qfile']['tmp_name'])) {
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
        if (!empty($data['item']) && is_array($data['item'])) {
          $ins = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, linkId, text, type) VALUES (?, ?, ?, ?)");
          foreach ($data['item'] as $it) {
            $ins->execute([$qid, $it['linkId'] ?? uniqid('q'), $it['text'] ?? '', $it['type'] ?? 'text']);
          }
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
        $ins = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, linkId, text, type) VALUES (?, ?, ?, ?)");
        foreach ($xml->item as $it) {
          $linkId = (string)($it['linkId'] ?? uniqid('q'));
          $text = (string)($it->text ?? '');
          $type = (string)($it['type'] ?? 'text');
          $ins->execute([$qid, $linkId, $text, $type]);
        }
        $msg = 'Questionnaire imported (XML).';
      } else {
        throw new Exception('Unsupported file type.');
      }
    } catch (Exception $e) {
      $msg = 'Import failed: ' . $e->getMessage();
    }
  }
}

$questionnaires = $pdo->query("SELECT * FROM questionnaire ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['manage_questionnaires']) ?></h2>
    <?php if($msg): ?><div class="alert alert-info"><?=$msg?></div><?php endif; ?>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['create_questionnaire']) ?></h3></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="create_q">
          <div class="form-group"><label><?= htmlspecialchars($t['title']) ?></label><input class="form-control" name="title" required></div>
          <div class="form-group"><label><?= htmlspecialchars($t['description']) ?></label><textarea class="form-control" name="description"></textarea></div>
          <button class="btn btn-primary"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['add_question']) ?></h3></div>
      <div class="card-body">
        <form method="post">
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
            <div class="form-group col-md-3"><label><?= htmlspecialchars($t['linkId']) ?></label><input class="form-control" name="linkId" placeholder="optional"></div>
            <div class="form-group col-md-4"><label><?= htmlspecialchars($t['text']) ?></label><input class="form-control" name="text" required></div>
            <div class="form-group col-md-2">
              <label><?= htmlspecialchars($t['type']) ?></label>
              <select class="form-control" name="type">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="boolean">Yes/No</option>
              </select>
            </div>
          </div>
          <button class="btn btn-secondary"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['import_questionnaire']) ?></h3></div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="import_q">
          <div class="form-group">
            <input type="file" name="qfile" accept=".json,.xml" class="form-control" required>
          </div>
          <button class="btn btn-primary"><?= htmlspecialchars($t['import']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['existing_questionnaires']) ?></h3></div>
      <div class="card-body">
        <?php foreach($questionnaires as $q): 
          $items = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ?");
          $items->execute([$q['id']]);
          $rows = $items->fetchAll(); ?>
          <div class="mb-3 p-2 border rounded">
            <h5><?=htmlspecialchars($q['title'])?></h5>
            <ul>
              <?php foreach($rows as $it): ?>
                <li><code><?=htmlspecialchars($it['linkId'])?></code> — <?=htmlspecialchars($it['text'])?> <em>(<?=$it['type']?>)</em></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
