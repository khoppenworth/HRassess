<?php
session_start();
$lang = $_GET['lang'] ?? 'en';
$_SESSION['lang'] = in_array($lang, ['en','am','fr'], true) ? $lang : 'en';
$back = $_SERVER['HTTP_REFERER'] ?? '/';
header('Location: ' . $back);
exit;
?>