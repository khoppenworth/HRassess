<?php
session_start();
$lang = $_GET['lang'] ?? 'en';
$allowed = ['en','am','fr'];
$_SESSION['lang'] = in_array($lang, $allowed, true) ? $lang : 'en';
$back = $_SERVER['HTTP_REFERER'] ?? '/';
header('Location: ' . $back);
exit;
?>