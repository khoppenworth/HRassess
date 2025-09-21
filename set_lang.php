<?php
session_start();
$lang = $_GET['lang'] ?? 'en';
$_SESSION['lang'] = in_array($lang, ['en','am','fr'], true) ? $lang : 'en';
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
?>