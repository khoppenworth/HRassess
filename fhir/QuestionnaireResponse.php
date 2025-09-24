<?php
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $data=json_decode(file_get_contents('php://input'),true);
  if(!$data){ http_response_code(400); echo json_encode(['error'=>'invalid']); exit; }
  $user_id=$data['user_id']??null; $qid=$data['questionnaire']??null;
  $pdo->prepare("INSERT INTO questionnaire_response(user_id,questionnaire_id,status,score) VALUES(?,?,?,0)")->execute([$user_id,$qid,'submitted']);
  $rid=$pdo->lastInsertId();
  foreach($data['item'] as $it){ $ans=json_encode($it['answer']); $pdo->prepare("INSERT INTO questionnaire_response_item(response_id,linkId,answer) VALUES(?,?,?)")->execute([$rid,$it['linkId'],$ans]); }
  echo json_encode(['resourceType'=>'QuestionnaireResponse','id'=>$rid]);
  exit;
} else {
  $rows=$pdo->query("SELECT * FROM questionnaire_response")->fetchAll();
  echo json_encode(['resourceType'=>'Bundle','entry'=>$rows]);
}
?>