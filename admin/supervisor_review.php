<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../i18n.php';
auth_required(['supervisor','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['response_id'], $_POST['action'])) {
  $rid = (int)$_POST['response_id'];
  if ($_POST['action'] === 'approve') {
    $stmt = $pdo->prepare("UPDATE questionnaire_response SET status='approved', reviewed_by=?, reviewed_at=NOW(), review_comment=? WHERE id=?");
    $stmt->execute([$_SESSION['user_id'], $_POST['review_comment'] ?? null, $rid]);
    $msg = 'Approved.';
  } elseif ($_POST['action'] === 'reject') {
    $stmt = $pdo->prepare("UPDATE questionnaire_response SET status='rejected', reviewed_by=?, reviewed_at=NOW(), review_comment=? WHERE id=?");
    $stmt->execute([$_SESSION['user_id'], $_POST['review_comment'] ?? null, $rid]);
    $msg = 'Rejected.';
  }
}

// Pending
$pending = $pdo->query("
  SELECT r.id, r.created_at, u.username, q.title
  FROM questionnaire_response r
  JOIN users u ON u.id = r.user_id
  JOIN questionnaire q ON q.id = r.questionnaire_id
  WHERE r.status='submitted'
  ORDER BY r.created_at ASC
")->fetchAll();

// Detail view
$view = null; $items = [];
if (isset($_GET['id'])) {
  $rid = (int)$_GET['id'];
  $stmt = $pdo->prepare("
    SELECT r.*, u.username, q.title 
    FROM questionnaire_response r
    JOIN users u ON u.id = r.user_id
    JOIN questionnaire q ON q.id = r.questionnaire_id
    WHERE r.id = ?
  "); $stmt->execute([$rid]);
  $view = $stmt->fetch();
  if ($view) {
    $it = $pdo->prepare("SELECT i.text, i.linkId, ri.answer FROM questionnaire_response_item ri JOIN questionnaire_item i ON i.linkId = ri.linkId WHERE ri.response_id = ?");
    $it->execute([$rid]);
    $items = $it->fetchAll();
  }
}

include __DIR__ . '/../templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['pending_reviews']) ?></h2>
    <?php if($msg): ?><div class="alert alert-info"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <div class="row">
      <div class="col-md-5">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['pending_reviews']) ?></h3></div>
          <div class="card-body">
            <ul class="list-group">
              <?php foreach($pending as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>#<?=$p['id']?> — <?=htmlspecialchars($p['title'])?> (<?=htmlspecialchars($p['username'])?>)</span>
                  <a class="btn btn-sm btn-outline-primary" href="/admin/supervisor_review.php?id=<?=$p['id']?>">Open</a>
                </li>
              <?php endforeach; ?>
              <?php if(!$pending): ?><li class="list-group-item">No pending.</li><?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-7">
        <?php if($view): ?>
          <div class="card">
            <div class="card-header"><h3 class="card-title">#<?=$view['id']?> — <?=htmlspecialchars($view['title'])?> (<?=htmlspecialchars($view['username'])?>)</h3></div>
            <div class="card-body">
              <?php foreach($items as $it): ?>
                <div class="mb-2">
                  <strong><?=htmlspecialchars($it['text'])?></strong><br>
                  <div><?=htmlspecialchars($it['answer'])?></div>
                </div>
              <?php endforeach; ?>
              <form method="post" class="mt-3">
                <input type="hidden" name="response_id" value="<?=$view['id']?>">
                <div class="form-group">
                  <label><?= htmlspecialchars($t['review_comment']) ?></label>
                  <textarea class="form-control" name="review_comment"></textarea>
                </div>
                <button class="btn btn-success" name="action" value="approve"><?= htmlspecialchars($t['approve']) ?></button>
                <button class="btn btn-danger" name="action" value="reject"><?= htmlspecialchars($t['reject']) ?></button>
              </form>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
