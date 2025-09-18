<!-- Placeholder for header.php -->
<?php
// templates/header.php
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link" href="/logout.php">Logout (<?=htmlspecialchars($_SESSION['username'] ?? '')?>)</a>
    </li>
  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link"><span class="brand-text font-weight-light">EPSS</span></a>
  <div class="sidebar">
    <nav class="mt-2"><ul class="nav nav-pills nav-sidebar flex-column">
      <li class="nav-item"><a href="/dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
      <li class="nav-item"><a href="/submit_assessment.php" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Submit Assessment</p></a></li>
      <?php if(current_user_role()==='admin'): ?>
      <li class="nav-item"><a href="/admin/dashboard.php" class="nav-link"><i class="nav-icon fas fa-cog"></i><p>Admin</p></a></li>
      <?php endif; ?>
    </ul></nav>
  </div>
</aside>
