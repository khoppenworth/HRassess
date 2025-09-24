<?php
require_once __DIR__.'/../config.php';
auth_required(['admin','supervisor']);
$t = load_lang($_SESSION['lang'] ?? 'en');

if ($_SERVER['REQUEST_METHOD']==='POST') {
  csrf_check();
  $id = (int)$_POST['id'];
  $status = in_array($_POST['status'], ['approved','rejected'], true) ? $_POST['status'] : 'submitted';
  $stm = $pdo->prepare("UPDATE questionnaire_response SET status=?, reviewed_by=?, reviewed_at=NOW(), review_comment=? WHERE id=?");
  $stm->execute([$status, $_SESSION['user']['id'], $_POST['review_comment'] ?? null, $id]);
}
$rows = $pdo->query("SELECT qr.*, u.username, q.title FROM questionnaire_response qr JOIN users u ON u.id=qr.user_id JOIN questionnaire q ON q.id=qr.questionnaire_id WHERE qr.status='submitted' ORDER BY qr.created_at ASC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Review Queue</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/../templates/header.php'; ?>
<section class="content"><div class="container-fluid">
<div class="card"><div class="card-header"><h3>Pending Submissions</h3></div><div class="card-body">
<table class="table table-sm"><thead><tr><th>ID</th><th>User</th><th>Questionnaire</th><th>Score</th><th>Action</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?=$r['id']?></td>
  <td><?=htmlspecialchars($r['username'])?></td>
  <td><?=htmlspecialchars($r['title'])?></td>
  <td><?=$r['score']?></td>
  <td>
    <form method="post" class="form-inline">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <input type="hidden" name="id" value="<?=$r['id']?>">
      <select name="status" class="form-control form-control-sm">
        <option value="approved">Approve</option>
        <option value="rejected">Reject</option>
      </select>
      <input name="review_comment" class="form-control form-control-sm" placeholder="Comment">
      <button class="btn btn-sm btn-primary">Apply</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
</div></body></html>