<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $body = file_get_contents('php://input');
  $data = json_decode($body, true);
  if (!$data) { http_response_code(400); echo json_encode(['error'=>'Invalid JSON']); exit; }

  $questionnaire_id = (int)($data['questionnaire_id'] ?? 0);
  $user_id = $_SESSION['user_id'] ?? null;

  $stmt = $pdo->prepare("INSERT INTO responses (user_id, questionnaire_id, payload) VALUES (?,?,?)");
  $stmt->execute([$user_id, $questionnaire_id, json_encode($data)]);
  echo json_encode(['status'=>'ok', 'id'=>(int)$pdo->lastInsertId()]); exit;
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
