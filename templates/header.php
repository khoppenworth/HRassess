<?php
require_once __DIR__.'/../i18n.php';
$t=load_lang($_SESSION['lang']??'en');
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>EPSS</title></head><body>
<header><h1>EPSS Self-Assessment</h1>
<nav>
<a href="/dashboard.php">Dashboard</a> |
<a href="/submit_assessment.php"><?=$t['submit_assessment']??'Submit'?></a> |
<a href="/performance.php"><?=$t['performance']??'Performance'?></a> |
<a href="/logout.php">Logout</a>
</nav></header>
<main>
