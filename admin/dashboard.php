<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');
$users = $pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'] ?? 0;
$q = $pdo->query("SELECT COUNT(*) c FROM questionnaire")->fetch()['c'] ?? 0;
$r = $pdo->query("SELECT COUNT(*) c FROM questionnaire_response")->fetch()['c'] ?? 0;
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/../templates/header.php'; ?>
<section class="content">
<div class="container-fluid">
  <div class="row">
    <div class="col-md-4"><div class="card"><div class="card-body"><h5>Users</h5><p><?=$users?></p></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h5>Questionnaires</h5><p><?=$q?></p></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><h5>Responses</h5><p><?=$r?></p></div></div></div>
  </div>
  <a class="btn btn-primary" href="users.php">Manage Users</a>
  <a class="btn btn-secondary" href="questionnaire_manage.php">Manage Questionnaires</a>
  <a class="btn btn-info" href="supervisor_review.php">Review Queue</a>
  <a class="btn btn-success" href="analytics.php">Analytics</a>
  <a class="btn btn-warning" href="export.php">Export CSV</a>
</div>
</section>
<?php include __DIR__.'/../templates/footer.php'; ?>
</div></body></html>