<?php
require_once __DIR__ . '/config.php';
rate_limit_check();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (login($pdo, $_POST['username'] ?? '', $_POST['password'] ?? '')) {
    header('Location: /dashboard.php'); exit;
  }
  $err = "Invalid credentials";
}
include __DIR__ . '/templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card card-primary">
          <div class="card-header"><h3 class="card-title"><?=t('login')?></h3></div>
          <form method="post">
            <div class="card-body">
              <?php if (!empty($err)): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
              <div class="form-group">
                <label><?=t('username')?></label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="form-group">
                <label><?=t('password')?></label>
                <input type="password" name="password" class="form-control" required>
              </div>
            </div>
            <div class="card-footer">
              <button class="btn btn-primary"><?=t('login')?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/templates/foot.php'; ?>
