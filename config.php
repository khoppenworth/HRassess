<?php
declare(strict_types=1);
session_start();

// Load env with defaults
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'hrassess';
$DB_USER = getenv('DB_USER') ?: 'hr_user';
$DB_PASS = getenv('DB_PASS') ?: 'hr_pass';
$BASE_URL = getenv('BASE_URL') ?: 'http://localhost';

define('BASE_URL', $BASE_URL);

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  die("DB Connection failed.");
}

require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/i18n.php';
?>
