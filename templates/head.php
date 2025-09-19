<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?=t('app_title')?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- AdminLTE 3.2 & deps from CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <?php if (!empty($_SESSION['username'])): ?>
      <li class="nav-item"><a class="nav-link" href="/logout.php"><?=t('logout')?> (<?=htmlspecialchars($_SESSION['username'])?>)</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/" class="brand-link"><span class="brand-text font-weight-light"><?=t('app_title')?></span></a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item"><a href="/dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p><?=t('dashboard')?></p></a></li>
          <li class="nav-item"><a href="/admin/index.php" class="nav-link"><i class="nav-icon fas fa-tools"></i><p><?=t('admin')?></p></a></li>
          <li class="nav-item"><a href="/admin/users.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p><?=t('users')?></p></a></li>
          <li class="nav-item"><a href="/admin/questionnaires.php" class="nav-link"><i class="nav-icon fas fa-list"></i><p><?=t('questionnaires')?></p></a></li>
        </ul>
      </nav>
    </div>
  </aside>
  <div class="content-wrapper p-3">
