<?php
require_once __DIR__.'/config.php';
auth_required(['staff','supervisor','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

// Load questionnaires
$q = $pdo->query("SELECT id, title FROM questionnaire ORDER BY id DESC")->fetchAll();
$qid = (int)($_GET['qid'] ?? ($q[0]['id'] ?? 0));

// Handle POST
if ($_SERVER['REQUEST_METHOD']==='POST') {
    csrf_check();
    $qid = (int)($_POST['qid'] ?? 0);
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO questionnaire_response (user_id, questionnaire_id, status, created_at) VALUES (?,?, 'submitted', NOW())");
        $stmt->execute([$_SESSION['user']['id'], $qid]);
        $rid = (int)$pdo->lastInsertId();

        // compute a simple score: +1 for boolean true, +1 for non-empty strings
        $score = 0;
        $items = $pdo->prepare("SELECT id, linkId, type FROM questionnaire_item WHERE questionnaire_id=? ORDER BY order_index ASC");
        $items->execute([$qid]);
        foreach ($items as $it) {
            $name = 'item_'.$it['linkId'];
            $ans = $_POST[$name] ?? '';
            if ($it['type']==='boolean') {
                $val = ($ans==='1' || $ans==='true' || $ans==='on') ? 'true' : 'false';
                if ($val==='true') $score += 1;
                $a = json_encode([['valueBoolean'=>$val==='true']]);
            } else {
                if (trim((string)$ans)!=='') $score += 1;
                $a = json_encode([['valueString'=>trim((string)$ans)]]);
            }
            $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (response_id, linkId, answer) VALUES (?,?,?)");
            $ins->execute([$rid, $it['linkId'], $a]);
        }
        $upd = $pdo->prepare("UPDATE questionnaire_response SET score=? WHERE id=?");
        $upd->execute([$score, $rid]);
        $pdo->commit();
        header("Location: performance.php?msg=submitted");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $err = 'Error: ' . $e->getMessage();
    }
}

// Load selected questionnaire with items
$sections = [];
if ($qid) {
    $s = $pdo->prepare("SELECT * FROM questionnaire_section WHERE questionnaire_id=? ORDER BY order_index ASC");
    $s->execute([$qid]);
    $sections = $s->fetchAll();
    $items = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id=? ORDER BY order_index ASC");
    $items->execute([$qid]);
    $items = $items->fetchAll();
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title><?=t($t,'submit_assessment','Submit Assessment')?></title>
<link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/css/styles.css">
</head><body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/templates/header.php'; ?>
<section class="content">
<div class="container-fluid">
  <div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title"><?=t($t,'submit_assessment','Submit Assessment')?></h3>
    </div>
    <div class="card-body">
      <?php if (!empty($err)): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
      <form method="get" class="mb-3">
        <div class="form-group">
          <label><?=t($t,'select_questionnaire','Select questionnaire')?></label>
          <select name="qid" class="form-control" onchange="this.form.submit()">
            <?php foreach ($q as $row): ?>
              <option value="<?=$row['id']?>" <?=($row['id']==$qid?'selected':'')?>><?=htmlspecialchars($row['title'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
      <?php if ($qid): ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?=csrf_token()?>">
        <input type="hidden" name="qid" value="<?=$qid?>">
        <?php foreach ($sections as $sec): ?>
          <h5 class="mt-3"><?=htmlspecialchars($sec['title'])?></h5>
          <p class="text-muted"><?=htmlspecialchars($sec['description'])?></p>
          <hr>
          <?php foreach ($items as $it): if ((int)$it['section_id'] !== (int)$sec['id']) continue; ?>
            <div class="form-group">
              <label><?=htmlspecialchars($it['text'])?></label>
              <?php if ($it['type']==='boolean'): ?>
                <div><input type="checkbox" name="item_<?=$it['linkId']?>"></div>
              <?php elseif ($it['type']==='textarea'): ?>
                <textarea name="item_<?=$it['linkId']?>" class="form-control" rows="3"></textarea>
              <?php else: ?>
                <input name="item_<?=$it['linkId']?>" class="form-control">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
        <button class="btn btn-success"><?=t($t,'submit','Submit')?></button>
      </form>
      <?php else: ?>
        <p><?=t($t,'no_questionnaire','No questionnaire found.')?></p>
      <?php endif; ?>
    </div>
  </div>
</div>
</section>
<?php include __DIR__.'/templates/footer.php'; ?>
</div></body></html>