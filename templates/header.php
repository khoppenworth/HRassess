<?php
require_once __DIR__ . '/../i18n.php';
$t = load_lang($_SESSION['lang'] ?? 'en');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8">
  <title>EPSS Self-Assessment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><a class="nav-link" href="/profile.php"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($t['profile']) ?></a></li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="langMenu" data-toggle="dropdown">🌐 <?= strtoupper($_SESSION['lang'] ?? 'EN') ?></a>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="/set_lang.php?lang=en">English</a>
        <a class="dropdown-item" href="/set_lang.php?lang=am">Amharic</a>
        <a class="dropdown-item" href="/set_lang.php?lang=fr">Français</a>
      </div>
    </li>
    <?php if(isset($_SESSION['user_id'])): ?>
      <li class="nav-item"><a href="/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <?= htmlspecialchars($t['logout']) ?></a></li>
    <?php endif; ?>
  </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link">
    <span class="brand-text font-weight-light">EPSS Self-Assessment</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <?php if(($_SESSION['role'] ?? '') === 'admin'): ?>
          <li class="nav-item"><a href="/admin/dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p><?= htmlspecialchars($t['admin_dashboard']) ?></p></a></li>
          <li class="nav-item"><a href="/admin/users.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p><?= htmlspecialchars($t['users']) ?></p></a></li>
          <li class="nav-item"><a href="/admin/questionnaire_manage.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p><?= htmlspecialchars($t['manage_questionnaires']) ?></p></a></li>
          <li class="nav-item"><a href="/admin/supervisor_review.php" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p><?= htmlspecialchars($t['pending_reviews']) ?></p></a></li>
          <li class="nav-item"><a href="/admin/export.php" class="nav-link"><i class="nav-icon fas fa-download"></i><p><?= htmlspecialchars($t['export_csv']) ?></p></a></li>
        <?php elseif(($_SESSION['role'] ?? '') === 'supervisor'): ?>
          <li class="nav-item"><a href="/admin/supervisor_review.php" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p><?= htmlspecialchars($t['pending_reviews']) ?></p></a></li>
        <?php else: ?>
          <li class="nav-item"><a href="/dashboard.php" class="nav-link"><i class="nav-icon fas fa-home"></i><p><?= htmlspecialchars($t['dashboard']) ?></p></a></li>
          <li class="nav-item"><a href="/submit_assessment.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p><?= htmlspecialchars($t['submit_assessment']) ?></p></a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>

<div class="content-wrapper p-3">
