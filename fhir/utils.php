<!-- Placeholder for utils.php -->
<?php
// fhir/utils.php
require_once __DIR__ . '/../config.php';

function json_response($data, $status=200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

function load_questionnaire_by_fhirid($fhir_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM questionnaire WHERE fhir_id = ?");
    $stmt->execute([$fhir_id]);
    $q = $stmt->fetch();
    if (!$q) return null;

    $stmt = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$q['id']]);
    $items = $stmt->fetchAll();
    $q['items'] = $items;
    return $q;
}
