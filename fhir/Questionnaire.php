<?php
require_once __DIR__ . '/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  json_response(["error"=>"Method not allowed"], 405);
}

$id = $_GET['id'] ?? $_GET['fhir_id'] ?? null;
if (!$id) {
  $rows = $pdo->query("SELECT id, title, description FROM questionnaire ORDER BY created_at DESC")->fetchAll();
  $bundle = ["resourceType"=>"Bundle","type"=>"searchset","entry"=>[]];
  foreach ($rows as $r) {
    $bundle["entry"][] = ["resource"=>[
      "resourceType"=>"Questionnaire",
      "id" => (string)$r["id"],
      "title" => $r["title"],
      "description" => $r["description"]
    ]];
  }
  json_response($bundle);
}

$stmt = $pdo->prepare("SELECT * FROM questionnaire WHERE id = ?");
$stmt->execute([(int)$id]);
$q = $stmt->fetch();
if (!$q) json_response(["error"=>"Not found"],404);

$it = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ?");
$it->execute([$q['id']]);
$items = [];
foreach ($it as $row) {
  $items[] = ["linkId"=>$row["linkId"], "text"=>$row["text"], "type"=>$row["type"]];
}

json_response([
  "resourceType"=>"Questionnaire",
  "id" => (string)$q["id"],
  "title"=>$q["title"],
  "description"=>$q["description"],
  "item"=>$items
]);
