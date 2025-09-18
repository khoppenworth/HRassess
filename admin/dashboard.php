<!-- Placeholder for dashboard.php -->
<?php
// admin/dashboard.php
require_once __DIR__ . '/../config.php';
require_admin();
$stmt = $pdo->query("SELECT COUNT(*) FROM users"); $user_count = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM questionnaire_response"); $resp_count = $stmt->fetchColumn();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin</title><link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css"></head>
<body class="hold-transition sidebar-mini">
<div class="wrapper"><?php include __DIR__.'/../templates/header.php'; ?>
<div class="content-wrapper"><section class="content-header"><h1>Admin Dashboard</h1></section>
<section class="content"><div class="container-fluid">
  <div class="row">
    <div class="col-6"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Users</span><span class="info-box-number"><?=htmlspecialchars($user_count)?></span></div></div></div>
    <div class="col-6"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-file-alt"></i></span><div class="info-box-content"><span class="info-box-text">Responses</span><span class="info-box-number"><?=htmlspecialchars($resp_count)?></span></div></div></div>
  </div>
  <a href="users.php" class="btn btn-primary">Manage users</a>
  <a href="questionnaire_manage.php" class="btn btn-secondary">Manage questionnaires</a>
  <a href="export.php" class="btn btn-info">Export CSV</a>
</div></section></div><?php include __DIR__.'/../templates/footer.php'; ?></div>
<script src="/assets/adminlte/dist/js/adminlte.min.js"></script></body></html>
