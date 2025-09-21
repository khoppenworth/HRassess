<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';
$t = load_lang($_SESSION['lang'] ?? 'en') ?: [];

if (isset($_SESSION['user_id'])) {
  if (!isset($_SESSION['role'])) $_SESSION['role'] = 'staff';
  $dest = ($_SESSION['role'] === 'admin') ? '/admin/dashboard.php'
        : (($_SESSION['role'] === 'supervisor') ? '/admin/supervisor_review.php' : '/dashboard.php');
  header('Location: ' . $dest); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');
  if ($username && $password) {
    try {
      $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
      $stmt->execute([$username]);
      $user = $stmt->fetch();
      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        log_action($pdo, (int)$user['id'], 'login', ['username'=>$username]);
        $dest = ($user['role']==='admin') ? '/admin/dashboard.php' : (($user['role']==='supervisor') ? '/admin/supervisor_review.php' : '/dashboard.php');
        header('Location: ' . $dest); exit;
      } else {
        $error = 'Invalid username or password.';
      }
    } catch (Exception $e) {
      $error = 'Login failed: ' . $e->getMessage();
    }
  } else {
    $error = 'Please enter username and password.';
  }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($t['login_title'] ?? 'Login') ?> - EPSS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>EPSS</b> Self-Assessment</div>
  <div class="card">
    <div class="card-body login-card-body">
      <?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
      <form method="post">
        <div class="input-group mb-3">
          <input name="username" class="form-control" placeholder="<?= htmlspecialchars($t['username'] ?? 'Username') ?>" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="<?= htmlspecialchars($t['password'] ?? 'Password') ?>" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="row">
          <div class="col-8"><a href="#"><?= htmlspecialchars($t['need_help'] ?? 'Need help?') ?></a></div>
          <div class="col-4"><button class="btn btn-primary btn-block" type="submit"><?= htmlspecialchars($t['sign_in'] ?? 'Sign In') ?></button></div>
        </div>
      </form>
      <div class="mt-2 text-right">
        <a class="btn btn-sm btn-outline-secondary" href="/set_lang.php?lang=en">EN</a>
        <a class="btn btn-sm btn-outline-secondary" href="/set_lang.php?lang=am">AM</a>
        <a class="btn btn-sm btn-outline-secondary" href="/set_lang.php?lang=fr">FR</a>
      </div>
    </div>
  </div>
</div>
<script src="/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
