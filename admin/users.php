<!-- Placeholder for users.php -->
<?php
// admin/users.php
require_once __DIR__ . '/../config.php';
require_admin();

$action = $_GET['action'] ?? 'list';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { $err="Invalid CSRF"; }
    else {
        $username = $_POST['username']; $full = $_POST['full_name']; $email = $_POST['email']; $role = $_POST['role'];
        $pw = $_POST['password'] ?? bin2hex(random_bytes(4));
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, role) VALUES (?,?,?,?,?)");
        $stmt->execute([$username, $hash, $full, $email, $role]);
        $msg = "User created. Password: $pw";
    }
}

$users = $pdo->query("SELECT id,username,full_name,email,role,region,created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Users</title><link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper"><?php include __DIR__.'/../templates/header.php'; ?>
<div class="content-wrapper"><section class="content-header"><h1>Users</h1></section><section class="content"><div class="container-fluid">
<?php if(!empty($msg)) echo "<div class='alert alert-success'>".htmlspecialchars($msg)."</div>"; ?>
<div class="card"><div class="card-header"><h3>Create user</h3></div>
<div class="card-body">
<form method="post" action="?action=add"><?=csrf_tag()?>
  <div class="form-group"><label>Username</label><input name="username" class="form-control" required></div>
  <div class="form-group"><label>Full name</label><input name="full_name" class="form-control"></div>
  <div class="form-group"><label>Email</label><input name="email" type="email" class="form-control"></div>
  <div class="form-group"><label>Password (leave blank to auto-generate)</label><input name="password" class="form-control"></div>
  <div class="form-group"><label>Role</label><select name="role" class="form-control"><option value="staff">Staff</option><option value="admin">Admin</option></select></div>
  <button class="btn btn-primary" type="submit">Create</button>
</form>
</div></div>

<div class="card"><div class="card-header"><h3>User list</h3></div>
<div class="card-body"><table class="table table-bordered"><thead><tr><th>Username</th><th>Full name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead><tbody>
<?php foreach($users as $u) echo "<tr><td>".htmlspecialchars($u['username'])."</td><td>".htmlspecialchars($u['full_name'])."</td><td>".htmlspecialchars($u['email'])."</td><td>".htmlspecialchars($u['role'])."</td><td>".htmlspecialchars($u['created_at'])."</td></tr>"; ?>
</tbody></table></div></div>

</div></section></div><?php include __DIR__.'/../templates/footer.php'; ?></div><script src="/assets/adminlte/dist/js/adminlte.min.js"></script></body></html>
