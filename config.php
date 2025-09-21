<?php
declare(strict_types=1);
session_start();

// error_reporting(E_ALL); ini_set('display_errors','1'); // enable for debugging

define('DB_HOST','127.0.0.1');
define('DB_NAME','epss');
define('DB_USER','epss_user'); // TODO: change
define('DB_PASS','epss_pass'); // TODO: change

try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
    DB_USER, DB_PASS,
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]
  );
} catch (PDOException $e) {
  die("DB connection failed: " . $e->getMessage());
}

function auth_required(array $roles=[]): void {
  if (!isset($_SESSION['user_id'])) { header('Location: /index.php'); exit; }
  if ($roles && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true))) {
    http_response_code(403); echo 'Forbidden'; exit;
  }
}

function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf_token'];
}
function csrf_check(): void {
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    $t = $_POST['csrf_token'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf_token'] ?? '', $t)) { http_response_code(400); die('Invalid CSRF token'); }
  }
}

function log_action(PDO $pdo, ?int $user_id, string $action, array $meta=[]): void {
  try {
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, meta) VALUES (?,?,?)");
    $stmt->execute([$user_id, $action, $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null]);
  } catch (Throwable $e) {}
}
?>