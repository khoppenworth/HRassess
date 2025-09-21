<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../i18n.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') { csrf_check(); }

if (($_POST['action'] ?? '')==='create_user') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = in_array($_POST['role'], ['admin','supervisor','staff'], true) ? $_POST['role'] : 'staff';
  $stmt = $pdo->prepare("INSERT INTO users (username,password,role,full_name,email) VALUES (?,?,?,?,?)");
  $stmt->execute([$username,$password,$role, $_POST['full_name'] ?? null, $_POST['email'] ?? null]);
  log_action($pdo, (int)$_SESSION['user_id'], 'admin_create_user', ['username'=>$username,'role'=>$role]);
  $msg = 'User created.';
}

if (($_POST['action'] ?? '')==='update_user') {
  $id = (int)$_POST['id'];
  $role = in_array($_POST['role'], ['admin','supervisor','staff'], true) ? $_POST['role'] : 'staff';
  $pwd = trim($_POST['password'] ?? '');
  if ($pwd !== '') {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET role=?, password=? WHERE id=?")->execute([$role, $hash, $id]);
  } else {
    $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role, $id]);
  }
  log_action($pdo, (int)$_SESSION['user_id'], 'admin_update_user', ['user_id'=>$id,'role'=>$role,'pwd_changed'=>($pwd!=='')]);
  $msg = 'User updated.';
}

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
  log_action($pdo, (int)$_SESSION['user_id'], 'admin_delete_user', ['user_id'=>$id]);
  header('Location: /admin/users.php'); exit;
}

$users = $pdo->query("SELECT id, username, role, full_name, email, created_at FROM users ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['manage_users']) ?></h2>
    <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['create_user']) ?></h3></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action" value="create_user">
          <div class="form-row">
            <div class="form-group col-md-3"><label><?= htmlspecialchars($t['username']) ?></label><input class="form-control" name="username" required></div>
            <div class="form-group col-md-3"><label><?= htmlspecialchars($t['password']) ?></label><input class="form-control" name="password" required></div>
            <div class="form-group col-md-3"><label><?= htmlspecialchars($t['role']) ?></label>
              <select class="form-control" name="role"><option value="staff">Staff</option><option value="supervisor">Supervisor</option><option value="admin">Admin</option></select>
            </div>
            <div class="form-group col-md-3"><label><?= htmlspecialchars($t['full_name']) ?></label><input class="form-control" name="full_name"></div>
          </div>
          <div class="form-group"><label><?= htmlspecialchars($t['email']) ?></label><input class="form-control" type="email" name="email"></div>
          <button class="btn btn-primary" type="submit"><?= htmlspecialchars($t['create']) ?></button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['users']) ?></h3></div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead><tr><th>ID</th><th><?= htmlspecialchars($t['username']) ?></th><th><?= htmlspecialchars($t['role']) ?></th><th><?= htmlspecialchars($t['full_name']) ?></th><th><?= htmlspecialchars($t['email']) ?></th><th>Created</th><th><?= htmlspecialchars($t['actions']) ?></th></tr></thead>
          <tbody>
            <?php foreach($users as $u): ?>
              <tr>
                <td><?=$u['id']?></td>
                <td><?=htmlspecialchars($u['username'])?></td>
                <td>
                  <form method="post" class="form-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" value="<?=$u['id']?>">
                    <select class="form-control form-control-sm" name="role">
                      <option value="staff" <?= $u['role']==='staff'?'selected':'' ?>>Staff</option>
                      <option value="supervisor" <?= $u['role']==='supervisor'?'selected':'' ?>>Supervisor</option>
                      <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                    </select>
                </td>
                <td><?=htmlspecialchars($u['full_name'])?></td>
                <td><?=htmlspecialchars($u['email'])?></td>
                <td><?=$u['created_at']?></td>
                <td style="min-width:260px">
                    <input type="password" class="form-control form-control-sm d-inline-block" name="password" placeholder="<?= htmlspecialchars($t['new_password']) ?>" style="width:150px">
                    <button class="btn btn-sm btn.success"><?= htmlspecialchars($t['update']) ?></button>
                    <a class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')" href="/admin/users.php?delete=<?=$u['id']?>"><?= htmlspecialchars($t['delete']) ?></a>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
