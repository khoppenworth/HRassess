<?php
require_once __DIR__.'/../config.php';
$t = load_lang($_SESSION['lang'] ?? 'en');
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><a class="nav-link" href="/set_lang.php?lang=en">EN</a></li>
    <li class="nav-item"><a class="nav-link" href="/set_lang.php?lang=am">AM</a></li>
    <li class="nav-item"><a class="nav-link" href="/set_lang.php?lang=fr">FR</a></li>
    <li class="nav-item"><a class="nav-link" href="/logout.php"><?=t($t,'logout','Logout')?> (<?=htmlspecialchars($_SESSION['user']['username'] ?? '')?>)</a></li>
  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/dashboard.php" class="brand-link"><span class="brand-text font-weight-light">EPSS</span></a>
  <div class="sidebar">
    <nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column">
      <li class="nav-item"><a href="/dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p><?=t($t,'dashboard','Dashboard')?></p></a></li>
      <li class="nav-item"><a href="/submit_assessment.php" class="nav-link"><p><?=t($t,'submit_assessment','Submit Assessment')?></p></a></li>
      <li class="nav-item"><a href="/performance.php" class="nav-link"><p><?=t($t,'performance','Performance')?></p></a></li>
      <li class="nav-item"><a href="/profile.php" class="nav-link"><p><?=t($t,'profile','Profile')?></p></a></li>
      <?php if (in_array($_SESSION['user']['role'] ?? '', ['admin','supervisor'])): ?>
      <li class="nav-item"><a href="/admin/supervisor_review.php" class="nav-link"><p><?=t($t,'review_queue','Review Queue')?></p></a></li>
      <?php endif; ?>
      <?php if (($_SESSION['user']['role'] ?? '')==='admin'): ?>
      <li class="nav-item"><a href="/admin/dashboard.php" class="nav-link"><p><?=t($t,'admin','Admin')?></p></a></li>
      <?php endif; ?>
    </ul></nav>
  </div>
</aside>
<div class="content-wrapper p-3">