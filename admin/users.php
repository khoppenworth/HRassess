<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    if (isset($_POST['create'])) {
        $u = trim($_POST['username']);
        $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $r = in_array($_POST['role'], ['admin','supervisor','staff'], true) ? $_POST['role'] : 'staff';
        $stm = $pdo->prepare("INSERT INTO users (username,password,role,full_name,email) VALUES (?,?,?,?,?)");
        $stm->execute([$u,$p,$r, $_POST['full_name'] ?? null, $_POST['email'] ?? null]);
        $msg='User created';
    }
    if (isset($_POST['reset'])) {
        $id = (int)$_POST['id'];
        $p = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stm = $pdo->prepare("UPDATE users SET password=?, role=? WHERE id=?");
        $stm->execute([$p, $_POST['role'], $id]);
        $msg='Updated';
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    header('Location: users.php'); exit;
}
$rows = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Users</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/../templates/header.php'; ?>
<section class="content"><div class="container-fluid">
<?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<div class="card"><div class="card-header"><h3>Create User</h3></div><div class="card-body">
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<div class="form-row">
  <div class="form-group col-md-3"><input name="username" class="form-control" placeholder="username" required></div>
  <div class="form-group col-md-3"><input name="password" type="password" class="form-control" placeholder="password" required></div>
  <div class="form-group col-md-3">
    <select name="role" class="form-control">
      <option>staff</option><option>supervisor</option><option>admin</option>
    </select>
  </div>
  <div class="form-group col-md-3"><input name="full_name" class="form-control" placeholder="Full name"></div>
</div>
<div class="form-group"><input name="email" class="form-control" placeholder="Email"></div>
<button name="create" class="btn btn-primary">Create</button>
</form></div></div>

<div class="card mt-3"><div class="card-header"><h3>Users</h3></div><div class="card-body">
<table class="table table-sm"><thead><tr><th>ID</th><th>User</th><th>Role</th><th>Name</th><th>Email</th><th>Reset</th><th>Del</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?=$r['id']?></td>
  <td><?=htmlspecialchars($r['username'])?></td>
  <td><?=$r['role']?></td>
  <td><?=htmlspecialchars($r['full_name'])?></td>
  <td><?=htmlspecialchars($r['email'])?></td>
  <td>
    <form method="post" class="form-inline">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <input type="hidden" name="id" value="<?=$r['id']?>">
      <input name="new_password" placeholder="new pass" class="form-control form-control-sm" required>
      <select name="role" class="form-control form-control-sm">
        <option <?=$r['role']=='staff'?'selected':''?>>staff</option>
        <option <?=$r['role']=='supervisor'?'selected':''?>>supervisor</option>
        <option <?=$r['role']=='admin'?'selected':''?>>admin</option>
      </select>
      <button name="reset" class="btn btn-sm btn-secondary">Apply</button>
    </form>
  </td>
  <td><a class="btn btn-sm btn-danger" onclick="return confirm('Delete?')" href="users.php?delete=<?=$r['id']?>">X</a></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
</div></body></html>