<?php
require_once __DIR__ . '/../config.php';
function json_response($data, int $status=200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  exit;
}
?>