<?php
declare(strict_types=1);
session_start();
define('DB_HOST','127.0.0.1');
define('DB_NAME','epss');
define('DB_USER','epss_user');
define('DB_PASS','epss_pass');
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>