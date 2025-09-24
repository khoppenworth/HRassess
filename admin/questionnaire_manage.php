<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title=$_POST['title']??''; $desc=$_POST['description']??'';
  if($title){ $pdo->prepare("INSERT INTO questionnaire(title,description) VALUES(?,?)")->execute([$title,$desc]); $msg='Questionnaire created.'; }
}
$q=$pdo->query("SELECT * FROM questionnaire")->fetchAll();
?>
<h1>Manage Questionnaires</h1>
<?php if($msg) echo "<p>$msg</p>";?>
<form method=post><input name=title placeholder=Title required><input name=description placeholder=Description><button>Create</button></form>
<ul><?php foreach($q as $row):?><li><?=$row['title']?></li><?php endforeach;?></ul>