<?php
require_once __DIR__ . '/../config.php';
auth_required(['admin']);
csrf_validate();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['role'])) {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'] === 'admin' ? 'admin' : 'staff';
  $full_name = $_POST['full_name'] ?? null;
  $email = $_POST['email'] ?? null;
  $stmt = $pdo->prepare("INSERT INTO users (username,password,role,full_name,email) VALUES (?,?,?,?,?)");
  $stmt->execute([$username,$password,$role,$full_name,$email]);
  $msg = 'User created.';
}

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
  header('Location: /admin/users.php'); exit;
}

$users = $pdo->query("SELECT id, username, role, full_name, email, created_at FROM users ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Users</h3></div>
          <div class="card-body">
            <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
            <form method="post" class="mb-3">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <div class="form-row">
                <div class="col"><input name="username" class="form-control" placeholder="Username" required></div>
                <div class="col"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
                <div class="col"><input name="full_name" class="form-control" placeholder="Full name"></div>
                <div class="col"><input name="email" type="email" class="form-control" placeholder="Email"></div>
                <div class="col">
                  <select name="role" class="form-control">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
                <div class="col"><button class="btn btn-primary">Create</button></div>
              </div>
            </form>
            <table class="table table-bordered">
              <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Name</th><th>Email</th><th>Created</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                  <td><?=$u['id']?></td>
                  <td><?=htmlspecialchars($u['username'])?></td>
                  <td><?=$u['role']?></td>
                  <td><?=htmlspecialchars($u['full_name'] ?? '')?></td>
                  <td><?=htmlspecialchars($u['email'] ?? '')?></td>
                  <td><?=$u['created_at']?></td>
                  <td><a class="btn btn-danger btn-sm" href="/admin/users.php?delete=<?=$u['id']?>" onclick="return confirm('Delete user?')">Delete</a></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/foot.php'; ?>
