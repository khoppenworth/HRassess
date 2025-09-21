<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';
auth_required(['staff','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$qid = intval($_GET['qid'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM questionnaire WHERE id = ?");
$stmt->execute([$qid]);
$q = $stmt->fetch();
if (!$q) { http_response_code(404); die('Questionnaire not found'); }

$sections = $pdo->prepare("SELECT * FROM questionnaire_section WHERE questionnaire_id = ? ORDER BY order_index ASC, id ASC");
$sections->execute([$qid]);
$sections = $sections->fetchAll();

$items_stmt = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ? ORDER BY order_index ASC, id ASC");
$items_stmt->execute([$qid]);
$all_items = $items_stmt->fetchAll();

$items_by_section = [];
foreach ($all_items as $it) {
  $key = $it['section_id'] ?: 0;
  $items_by_section[$key][] = $it;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare("INSERT INTO questionnaire_response (user_id, questionnaire_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $qid]);
    $rid = $pdo->lastInsertId();
    $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (response_id, linkId, answer) VALUES (?, ?, ?)");
    foreach ($all_items as $it) {
      $val = $_POST['q_'.$it['id']] ?? '';
      $ins->execute([$rid, $it['linkId'], $val]);
    }
    $pdo->commit();
    log_action($pdo, (int)$_SESSION['user_id'], 'submit_assessment', ['questionnaire_id'=>$qid,'response_id'=>$rid]);
    $msg = $t['assessment_submitted'];
  } catch (Exception $e) {
    $pdo->rollBack();
    $msg = 'Error: '.$e->getMessage();
  }
}

include __DIR__ . '/templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?=htmlspecialchars($q['title'])?></h2>
    <p><?=htmlspecialchars($q['description'])?></p>
    <?php if($msg): ?><div class="alert alert-info"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="card">
        <div class="card-body">
          <?php if(isset($items_by_section[0])): ?>
            <h5 class="border-bottom pb-1">General</h5>
            <?php foreach($items_by_section[0] as $it): ?>
              <div class="form-group">
                <label><?=htmlspecialchars($it['text'])?></label>
                <?php if ($it['type']==='textarea'): ?>
                  <textarea class="form-control" name="q_<?=$it['id']?>"></textarea>
                <?php elseif ($it['type']==='boolean'): ?>
                  <select class="form-control" name="q_<?=$it['id']?>">
                    <option value=""><?= htmlspecialchars($t['select']) ?></option>
                    <option value="true"><?= htmlspecialchars($t['yes']) ?></option>
                    <option value="false"><?= htmlspecialchars($t['no']) ?></option>
                  </select>
                <?php else: ?>
                  <input class="form-control" name="q_<?=$it['id']?>" />
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php foreach ($sections as $sec): ?>
            <h5 class="mt-3 border-bottom pb-1"><?= htmlspecialchars($sec['title']) ?></h5>
            <?php if (!empty($sec['description'])): ?><p><?= htmlspecialchars($sec['description']) ?></p><?php endif; ?>
            <?php foreach ($items_by_section[$sec['id']] ?? [] as $it): ?>
              <div class="form-group">
                <label><?=htmlspecialchars($it['text'])?></label>
                <?php if ($it['type']==='textarea'): ?>
                  <textarea class="form-control" name="q_<?=$it['id']?>"></textarea>
                <?php elseif ($it['type']==='boolean'): ?>
                  <select class="form-control" name="q_<?=$it['id']?>">
                    <option value=""><?= htmlspecialchars($t['select']) ?></option>
                    <option value="true"><?= htmlspecialchars($t['yes']) ?></option>
                    <option value="false"><?= htmlspecialchars($t['no']) ?></option>
                  </select>
                <?php else: ?>
                  <input class="form-control" name="q_<?=$it['id']?>" />
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </div>
        <div class="card-footer">
          <button class="btn btn-success" type="submit"><?= htmlspecialchars($t['submit']) ?></button>
          <a class="btn btn-secondary" href="/dashboard.php"><?= htmlspecialchars($t['cancel']) ?></a>
        </div>
      </div>
    </form>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
