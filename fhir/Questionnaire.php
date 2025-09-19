<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $q = $pdo->prepare("SELECT * FROM questionnaires WHERE id=?");
    $q->execute([$id]);
    $row = $q->fetch();
    if (!$row) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }
    $items = $pdo->prepare("SELECT * FROM questionnaire_items WHERE questionnaire_id=?");
    $items->execute([$id]);
    $itemsRows = $items->fetchAll();

    $bundle = [
      'resourceType' => 'Questionnaire',
      'id' => (string)$row['id'],
      'title' => $row['title'],
      'item' => []
    ];
    foreach ($itemsRows as $it) {
      $opts = $pdo->prepare("SELECT * FROM questionnaire_options WHERE item_id=?");
      $opts->execute([$it['id']]);
      $optRows = $opts->fetchAll();
      $item = [
        'linkId' => $it['link_id'],
        'text' => $it['question_text'],
        'type' => $it['type']
      ];
      if ($optRows) {
        $item['answerOption'] = array_map(fn($o)=>['valueString'=>$o['option_text']], $optRows);
      }
      $bundle['item'][] = $item;
    }
    echo json_encode($bundle); exit;
  } else {
    // list
    $rows = $pdo->query("SELECT id, title, created_at FROM questionnaires ORDER BY id DESC")->fetchAll();
    echo json_encode($rows); exit;
  }
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
