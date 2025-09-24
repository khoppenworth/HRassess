<?php
require_once __DIR__.'/config.php';
auth_required();
$t = load_lang($_SESSION['lang'] ?? 'en');
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $pass = $_POST['password'] ?? '';
    if (strlen($pass) < 6) {
        $msg = 'Password too short.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stm = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
        $stm->execute([$hash, $_SESSION['user']['id']]);
        $msg = 'Updated.';
    }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title><?=t($t,'profile','Profile')?></title>
<link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/css/styles.css">
</head><body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/templates/header.php'; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-info">
      <div class="card-header"><h3 class="card-title"><?=t($t,'change_password','Change Password')?></h3></div>
      <div class="card-body">
        <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
        <form method="post">
          <input type="hidden" name="csrf" value="<?=csrf_token()?>">
          <div class="form-group">
            <label><?=t($t,'new_password','New Password')?></label>
            <input type="password" name="password" class="form-control" required minlength="6">
          </div>
          <button class="btn btn-primary"><?=t($t,'save','Save')?></button>
        </form>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__.'/templates/footer.php'; ?>
</div></body></html>