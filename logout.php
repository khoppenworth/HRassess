<?php
require_once __DIR__ . '/config.php';
log_action($pdo, (int)($_SESSION['user_id'] ?? 0), 'logout', []);
session_unset(); session_destroy();
header('Location: /index.php'); exit;
?>