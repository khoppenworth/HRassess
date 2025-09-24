<?php
require_once __DIR__.'/config.php'; require_once __DIR__.'/i18n.php';
$t=load_lang($_SESSION['lang']??'en');
if(isset($_SESSION['user_id'])){ $d=($_SESSION['role']==='admin')?'/dashboard.php':'/dashboard.php'; header('Location:'.$d); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username']??''; $p=$_POST['password']??'';
  $st=$pdo->prepare('SELECT * FROM users WHERE username=?'); $st->execute([$u]); $user=$st->fetch();
  if($user && password_verify($p,$user['password'])){ $_SESSION['user_id']=$user['id'];$_SESSION['role']=$user['role']; header('Location:/dashboard.php');exit; } else {$error='Invalid';}
}
?>
<!DOCTYPE html><html><head><title>Login</title></head><body>
<?php if($error):?><p style='color:red'><?=$error?></p><?php endif;?>
<form method=post><input name=username><input type=password name=password><button>Login</button></form>
<a href='set_lang.php?lang=en'>EN</a> <a href='set_lang.php?lang=am'>AM</a> <a href='set_lang.php?lang=fr'>FR</a>
</body></html>