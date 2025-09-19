<?php
session_start();
require_once __DIR__ . '/config.php';
if(!isset($_SESSION['user_id'])||$_SESSION['role']!=='staff'){header('Location:index.php');exit;}
$user_id=$_SESSION['user_id'];
$qid=intval($_GET['qid']??0);
$stmt=$pdo->prepare("SELECT * FROM questionnaire WHERE id=?");$stmt->execute([$qid]);
$q=$stmt->fetch();if(!$q)die("Invalid questionnaire");
$stmt=$pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id=?");$stmt->execute([$qid]);
$items=$stmt->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
$pdo->beginTransaction();
try{$stmt=$pdo->prepare("INSERT INTO questionnaire_response (user_id,questionnaire_id) VALUES (?,?)");$stmt->execute([$user_id,$qid]);
$response_id=$pdo->lastInsertId();
foreach($items as $item){$answer=$_POST['q_'.$item['id']]??'';$stmt=$pdo->prepare("INSERT INTO questionnaire_response_item (response_id,linkId,answer) VALUES (?,?,?)");$stmt->execute([$response_id,$item['linkId'],$answer]);}
$pdo->commit();header("Location: dashboard.php?submitted=1");exit;}catch(Exception $e){$pdo->rollBack();die("Error:".$e->getMessage());}}
?><!DOCTYPE html><html><body><h2><?=htmlspecialchars($q['title'])?></h2><form method='post'><?php foreach($items as $i){ ?>
<p><?=htmlspecialchars($i['text'])?><br><?php if($i['type']==='text'){ ?><input name='q_<?=$i['id']?>'><?php }elseif($i['type']==='textarea'){ ?><textarea name='q_<?=$i['id']?>'></textarea><?php }elseif($i['type']==='boolean'){ ?><select name='q_<?=$i['id']?>'><option value=''>--Select--</option><option value='true'>Yes</option><option value='false'>No</option></select><?php } ?></p><?php } ?>
<button type='submit'>Submit</button></form><p><a href='dashboard.php'>Back</a></p></body></html>
