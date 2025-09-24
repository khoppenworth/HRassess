<?php
require_once __DIR__.'/../config.php';
auth_required(['admin','supervisor']);
if(isset($_GET['approve'])){
  $id=(int)$_GET['approve']; $pdo->prepare("UPDATE questionnaire_response SET status='approved' WHERE id=?")->execute([$id]);
}
if(isset($_GET['reject'])){
  $id=(int)$_GET['reject']; $pdo->prepare("UPDATE questionnaire_response SET status='rejected' WHERE id=?")->execute([$id]);
}
$res=$pdo->query("SELECT qr.id,u.username,qr.status,qr.created_at FROM questionnaire_response qr JOIN users u ON u.id=qr.user_id WHERE qr.status='submitted'")->fetchAll();
?>
<h1>Supervisor Reviews</h1>
<table border=1><tr><th>ID</th><th>User</th><th>Status</th><th>Action</th></tr>
<?php foreach($res as $r):?>
<tr><td><?=$r['id']?></td><td><?=$r['username']?></td><td><?=$r['status']?></td>
<td><a href='?approve=<?=$r['id']?>'>Approve</a> | <a href='?reject=<?=$r['id']?>'>Reject</a></td></tr>
<?php endforeach;?></table>