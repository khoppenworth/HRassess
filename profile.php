<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';
auth_required(['staff','admin','supervisor']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg = '';
$stmt = $pdo->prepare("SELECT username, full_name, email FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  csrf_check();
  $full = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pwd = trim($_POST['new_password'] ?? '');
  if ($pwd !== '') {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET full_name=?, email=?, password=? WHERE id=?")->execute([$full,$email,$hash,$_SESSION['user_id']]);
    log_action($pdo, (int)$_SESSION['user_id'], 'profile_update_password', []);
  } else {
    $pdo->prepare("UPDATE users SET full_name=?, email=? WHERE id=?")->execute([$full,$email,$_SESSION['user_id']]);
    log_action($pdo, (int)$_SESSION['user_id'], 'profile_update', []);
  }
  $msg = $t['update'] . ' OK';
  $stmt->execute([$_SESSION['user_id']]);
  $me = $stmt->fetch();
}

include __DIR__ . '/templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['profile']) ?></h2>
    <?php if($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="card">
        <div class="card-body">
          <div class="form-group"><label>Username</label><input class="form-control" value="<?= htmlspecialchars($me['username']) ?>" disabled></div>
          <div class="form-group"><label><?= htmlspecialchars($t['full_name']) ?></label><input class="form-control" name="full_name" value="<?= htmlspecialchars($me['full_name']) ?>"></div>
          <div class="form-group"><label><?= htmlspecialchars($t['email']) ?></label><input class="form-control" type="email" name="email" value="<?= htmlspecialchars($me['email']) ?>"></div>
          <div class="form-group"><label><?= htmlspecialchars($t['new_password']) ?></label><input class="form-control" type="password" name="new_password" placeholder="••••••••"></div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary"><?= htmlspecialchars($t['save']) ?></button>
        </div>
      </div>
    </form>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
