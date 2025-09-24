<?php
require_once __DIR__.'/../config.php';
auth_required(['admin','supervisor']);
header('Content-Type:text/csv'); header('Content-Disposition: attachment;filename=export.csv');
$out=fopen('php://output','w');
fputcsv($out,['id','user','questionnaire','status','created_at']);
$st=$pdo->query("SELECT qr.id,u.username,qr.questionnaire_id,qr.status,qr.created_at FROM questionnaire_response qr JOIN users u ON u.id=qr.user_id");
while($r=$st->fetch()){ fputcsv($out,$r); }
fclose($out);
?>