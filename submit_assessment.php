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

$stmt = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ?");
$stmt->execute([$qid]);
$items = $stmt->fetchAll();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare("INSERT INTO questionnaire_response (user_id, questionnaire_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $qid]);
    $rid = $pdo->lastInsertId();

    $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (response_id, linkId, answer) VALUES (?, ?, ?)");
    foreach ($items as $it) {
      $val = $_POST['q_'.$it['id']] ?? '';
      $ins->execute([$rid, $it['linkId'], $val]);
    }
    $pdo->commit();
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
      <div class="card">
        <div class="card-body">
          <?php foreach ($items as $it): ?>
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
