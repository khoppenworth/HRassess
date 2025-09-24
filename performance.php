<?php
require_once __DIR__.'/config.php';
auth_required(['staff','supervisor','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$stmt = $pdo->prepare("SELECT qr.*, q.title FROM questionnaire_response qr JOIN questionnaire q ON q.id=qr.questionnaire_id WHERE qr.user_id=? ORDER BY qr.created_at ASC");
$stmt->execute([$_SESSION['user']['id']]);
$rows = $stmt->fetchAll();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title><?=t($t,'performance','Performance')?></title>
<link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/css/styles.css">
</head><body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/templates/header.php'; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-success">
      <div class="card-header"><h3 class="card-title"><?=t($t,'your_trend','Your Score Trend')?></h3></div>
      <div class="card-body">
        <table class="table table-sm">
          <thead><tr><th><?=t($t,'date','Date')?></th><th><?=t($t,'questionnaire','Questionnaire')?></th><th><?=t($t,'score','Score')?></th><th><?=t($t,'status','Status')?></th></tr></thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?=htmlspecialchars($r['created_at'])?></td>
              <td><?=htmlspecialchars($r['title'])?></td>
              <td><?= (int)$r['score']?></td>
              <td><?=htmlspecialchars($r['status'])?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__.'/templates/footer.php'; ?>
</div></body></html>