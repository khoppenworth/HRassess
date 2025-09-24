<?php
require_once __DIR__.'/../config.php';
auth_required(['admin','supervisor']);
$rows=$pdo->query("SELECT u.username, COUNT(qr.id) as cnt, AVG(qr.score) as avgscore FROM users u LEFT JOIN questionnaire_response qr ON qr.user_id=u.id GROUP BY u.id")->fetchAll();
?>
<h1>Analytics</h1>
<table border=1><tr><th>User</th><th>Responses</th><th>Avg Score</th></tr>
<?php foreach($rows as $r):?>
<tr><td><?=$r['username']?></td><td><?=$r['cnt']?></td><td><?=$r['avgscore']?></td></tr>
<?php endforeach;?></table>