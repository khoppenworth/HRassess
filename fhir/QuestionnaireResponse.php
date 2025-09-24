<?php
require_once __DIR__.'/utils.php';
if ($_SERVER['REQUEST_METHOD']==='GET') {
  $entries = [];
  $rs = $pdo->query("SELECT * FROM questionnaire_response ORDER BY id DESC");
  foreach ($rs as $r) {
    $items = $pdo->prepare("SELECT linkId, answer FROM questionnaire_response_item WHERE response_id=?");
    $items->execute([$r['id']]);
    $entries[] = ["resource"=>[
      "resourceType"=>"QuestionnaireResponse",
      "id"=>$r['id'],
      "questionnaire"=>$r['questionnaire_id'],
      "status"=>$r['status'],
      "authored"=>$r['created_at'],
      "item"=>array_map(function($it){ return ["linkId"=>$it['linkId'],"answer"=>json_decode($it['answer'], true)]; }, $items->fetchAll())
    ]];
  }
  echo json_encode(bundle($entries));
  exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $data = json_decode(file_get_contents('php://input'), true);
  if (($data['resourceType'] ?? '')!=='QuestionnaireResponse') { http_response_code(400); echo json_encode(["error"=>"Invalid resourceType"]); exit; }
  $uid = (int)($data['user_id'] ?? 0);
  $qid = (int)($data['questionnaire'] ?? 0);
  if (!$uid || !$qid) { http_response_code(400); echo json_encode(["error"=>"user_id and questionnaire required"]); exit; }

  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare("INSERT INTO questionnaire_response (user_id, questionnaire_id, status, created_at) VALUES (?,?, 'submitted', NOW())");
    $stmt->execute([$uid, $qid]);
    $rid = (int)$pdo->lastInsertId();
    $score = 0;
    foreach (($data['item'] ?? []) as $it) {
      $ans = json_encode($it['answer'] ?? []);
      // crude scoring
      foreach (($it['answer'] ?? []) as $a) {
        if (isset($a['valueBoolean']) && $a['valueBoolean']===true) $score += 1;
        if (isset($a['valueString']) && trim((string)$a['valueString'])!=='') $score += 1;
      }
      $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (response_id, linkId, answer) VALUES (?,?,?)");
      $ins->execute([$rid, $it['linkId'] ?? '', $ans]);
    }
    $pdo->prepare("UPDATE questionnaire_response SET score=? WHERE id=?")->execute([$score, $rid]);
    $pdo->commit();
    echo json_encode(["id"=>$rid, "status"=>"created"]);
  } catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error"=>$e->getMessage()]);
  }
  exit;
}

http_response_code(405);
echo json_encode(["error"=>"Method not allowed"]);
?>