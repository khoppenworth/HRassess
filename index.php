<!-- Placeholder for index.php -->

<?php
// index.php
require_once 'config.php';
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login success
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $err = "Invalid credentials";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>EPSS Self-Assessment - Login</title>
  <link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
  <style>body{background:#f4f6f9}</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><a href="#"><b>EPSS</b> Self-Assessment</a></div>
  <div class="card">
    <div class="card-body login-card-body">
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" action="">
        <div class="input-group mb-3">
          <input name="username" class="form-control" placeholder="Username" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input name="password" type="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <?=csrf_tag()?>
        <div class="row">
          <div class="col-8"><a href="#">Need help?</a></div>
          <div class="col-4"><button type="submit" class="btn btn-primary btn-block">Sign In</button></div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
