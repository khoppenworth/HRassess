<?php
require_once __DIR__ . '/../config.php';
auth_required(['admin']);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=epss_responses_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['response_id','user_id','questionnaire_id','created_at','linkId','answer']);

$sql = "SELECT r.id as response_id, r.user_id, r.questionnaire_id, r.created_at, ri.linkId, ri.answer
        FROM questionnaire_response r
        JOIN questionnaire_response_item ri ON ri.response_id = r.id
        ORDER BY r.created_at DESC, r.id DESC";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
  fputcsv($out, $row);
}
fclose($out);
exit;
