<!-- Placeholder for export.php -->
<?php
// admin/export.php
require_once __DIR__ . '/../config.php';
require_admin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=questionnaire_responses_'.date('Ymd').'.csv');

$out = fopen('php://output','w');
fputcsv($out, ['response_fhir_id','questionnaire','user','authored','region','question_linkId','question_text','answer']);

$stmt = $pdo->query("SELECT qr.*, q.title as questionnaire_title, u.username FROM questionnaire_response qr LEFT JOIN questionnaire q ON qr.questionnaire_id=q.id LEFT JOIN users u ON qr.user_id=u.id ORDER BY qr.authored DESC");
while ($r = $stmt->fetch()) {
    $stmt2 = $pdo->prepare("SELECT qri.*, qi.text as qtext, qi.link_id as qlink FROM questionnaire_response_item qri LEFT JOIN questionnaire_item qi ON qri.questionnaire_item_id=qi.id WHERE qri.questionnaire_response_id = ?");
    $stmt2->execute([$r['id']]);
    while ($it = $stmt2->fetch()) {
        $ans = $it['answer_text'] ?? ($it['answer_boolean']!==null?($it['answer_boolean']?'true':'false') : ($it['answer_integer'] ?? ($it['answer_decimal'] ?? ($it['answer_date'] ?? ($it['answer_datetime'] ?? $it['answer_json'])))));
        fputcsv($out, [$r['fhir_id'], $r['questionnaire_title'], $r['username'], $r['authored'], $r['region'], $it['link_id'], $it['qtext'], $ans]);
    }
}
fclose($out);
exit;
