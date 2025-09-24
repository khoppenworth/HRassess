<?php
require_once __DIR__.'/config.php';
auth_required();
$t = load_lang($_SESSION['lang'] ?? 'en');
$user = current_user();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Dashboard</title>
<link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/css/styles.css">
</head><body class="hold-transition sidebar-mini">
<div class="wrapper">
<?php include __DIR__.'/templates/header.php'; ?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary">
          <div class="card-header"><h3 class="card-title"><?=t($t,'welcome','Welcome')?>, <?=htmlspecialchars($user['full_name'] ?? $user['username'])?></h3></div>
          <div class="card-body">
            <p><?=t($t,'dashboard_intro','Use the menu to submit self-assessments, track performance, or administer the system.')?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__.'/templates/footer.php'; ?>
</div></body></html>