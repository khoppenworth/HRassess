<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username']??''; $p=$_POST['password']??''; $role=$_POST['role']??'staff';
  if($u && $p){
    $hash=password_hash($p,PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users(username,password,role) VALUES(?,?,?)")->execute([$u,$hash,$role]);
    $msg="User created.";
  }
}
$users=$pdo->query("SELECT * FROM users")->fetchAll();
?>
<h1>Manage Users</h1>
<?php if($msg) echo "<p>$msg</p>";?>
<form method=post><input name=username placeholder=Username required><input type=password name=password required><select name=role><option>staff</option><option>supervisor</option><option>admin</option></select><button>Create</button></form>
<table border=1><tr><th>ID</th><th>User</th><th>Role</th></tr>
<?php foreach($users as $u):?>
<tr><td><?=$u['id']?></td><td><?=$u['username']?></td><td><?=$u['role']?></td></tr>
<?php endforeach;?>
</table>