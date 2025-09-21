<?php
require_once __DIR__ . '/utils.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
  $id = $_GET['id'] ?? null;
  $questionnaire = $_GET['questionnaire'] ?? null;

  if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM questionnaire_response WHERE id = ?");
    $stmt->execute([(int)$id]);
    $resp = $stmt->fetch();
    if (!$resp) json_response(["error"=>"Not found"],404);

    $items = $pdo->prepare("SELECT linkId, answer FROM questionnaire_response_item WHERE response_id = ?");
    $items->execute([$resp['id']]);
    $fitems = [];
    foreach ($items as $i) {
      $fitems[] = ["linkId"=>$i['linkId'], "answer"=>[["valueString"=>$i['answer']]]];
    }

    json_response([
      "resourceType"=>"QuestionnaireResponse",
      "id" => (string)$resp["id"],
      "questionnaire" => (string)$resp["questionnaire_id"],
      "status"=>$resp["status"],
      "authored"=>$resp["created_at"],
      "subject"=>["reference"=>"User/".$resp["user_id"]],
      "item"=>$fitems
    ]);
  }

  if ($questionnaire) {
    $stmt = $pdo->prepare("SELECT id, created_at, status FROM questionnaire_response WHERE questionnaire_id = ? ORDER BY id DESC");
    $stmt->execute([(int)$questionnaire]);
    $entries = [];
    foreach ($stmt as $r) {
      $entries[] = ["resource"=>[
        "resourceType"=>"QuestionnaireResponse",
        "id"=>(string)$r["id"],
        "authored"=>$r["created_at"],
        "status"=>$r["status"]
      ]];
    }
    json_response(["resourceType"=>"Bundle","type"=>"searchset","entry"=>$entries]);
  }

  json_response(["error"=>"Provide id or questionnaire param"], 400);
}

if ($method === 'POST') {
  $payload = json_decode(file_get_contents('php://input'), true);
  if (!$payload) json_response(["error"=>"Invalid JSON"],400);
  $qid = (int)($payload["questionnaire"] ?? 0);
  if (!$qid) json_response(["error"=>"Missing questionnaire"],400);

  $stmt = $pdo->prepare("INSERT INTO questionnaire_response (user_id, questionnaire_id) VALUES (?, ?)");
  $user_id = (int)($payload["user_id"] ?? 0);
  if (!$user_id) $user_id = $_SESSION['user_id'] ?? 0;
  $stmt->execute([$user_id, $qid]);
  $rid = $pdo->lastInsertId();

  $items = $payload["item"] ?? [];
  $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (response_id, linkId, answer) VALUES (?, ?, ?)");
  foreach ($items as $it) {
    $linkId = $it["linkId"] ?? "";
    $ans = "";
    if (isset($it["answer"][0]["valueString"])) $ans = $it["answer"][0]["valueString"];
    elseif (isset($it["answer"][0]["valueBoolean"])) $ans = $it["answer"][0]["valueBoolean"] ? "true" : "false";
    elseif (isset($it["answer"][0]["valueInteger"])) $ans = (string)$it["answer"][0]["valueInteger"];
    $ins->execute([$rid, $linkId, $ans]);
  }

  json_response(["resourceType"=>"QuestionnaireResponse","id"=>(string)$rid,"status"=>"created"], 201);
}

json_response(["error"=>"Method not allowed"], 405);
