<?php
declare(strict_types=1);
session_start();

define('DB_HOST','127.0.0.1');
define('DB_NAME','epss');
define('DB_USER','epss_user');   // TODO: set your MySQL user
define('DB_PASS','epss_pass');   // TODO: set your MySQL password

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

function auth_required(array $roles = []): void {
    if (!isset($_SESSION['user_id'])) { header('Location: /index.php'); exit; }
    if ($roles && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true))) {
        http_response_code(403); echo "Forbidden"; exit;
    }
}
?>