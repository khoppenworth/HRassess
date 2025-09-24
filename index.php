<?php
require_once __DIR__.'/config.php';
$t = load_lang($_SESSION['lang'] ?? 'en');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['user'] = ['id'=>$u['id'], 'username'=>$u['username'], 'role'=>$u['role'], 'full_name'=>$u['full_name']];
        header('Location: dashboard.php'); exit;
    } else {
        $err = t($t,'invalid_login','Invalid username or password');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>EPSS - Login</title>
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>EPSS</b> Self-Assessment</div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"><?=t($t,'please_sign_in','Please sign in')?></p>
      <?php if (!empty($err)): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?=csrf_token()?>">
        <div class="input-group mb-3">
          <input name="username" class="form-control" placeholder="<?=t($t,'username','Username')?>" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="<?=t($t,'password','Password')?>" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="set_lang.php?lang=en">EN</a> | <a href="set_lang.php?lang=am">AM</a> | <a href="set_lang.php?lang=fr">FR</a>
          </div>
          <div class="col-4"><button class="btn btn-primary btn-block"><?=t($t,'sign_in','Sign In')?></button></div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>