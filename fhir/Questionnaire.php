<?php
require_once __DIR__.'/../config.php';
header('Content-Type: application/json');
$rows=$pdo->query("SELECT * FROM questionnaire")->fetchAll();
$out=['resourceType'=>'Bundle','type'=>'collection','entry'=>[]];
foreach($rows as $q){
  $items=$pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id=?");
  $items->execute([$q['id']]);
  $arr=[]; foreach($items as $i){ $arr[]=['linkId'=>$i['linkId'],'text'=>$i['text'],'type'=>$i['type']]; }
  $out['entry'][]=['resource'=>['resourceType'=>'Questionnaire','id'=>$q['id'],'title'=>$q['title'],'description'=>$q['description'],'item'=>$arr]];
}
echo json_encode($out);
?>