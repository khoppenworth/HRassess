<?php
session_start();
require_once __DIR__ . '/config.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='staff'){ header('Location: index.php'); exit; }
$user_id = $_SESSION['user_id'];
$stmt=$pdo->query("SELECT * FROM questionnaire ORDER BY created_at DESC");
$qs=$stmt->fetchAll();
?><!DOCTYPE html><html><body><h2>Staff Dashboard</h2><?php foreach($qs as $q){ ?>
<p><?=htmlspecialchars($q['title'])?> - <a href="submit_assessment.php?qid=<?=$q['id']?>">Start</a></p>
<?php } ?>
<p><a href="logout.php">Logout</a></p>
</body></html>