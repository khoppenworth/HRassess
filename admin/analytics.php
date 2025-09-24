<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

// Simple aggregates
$avg = $pdo->query("SELECT u.username, AVG(score) avg_score, COUNT(*) cnt FROM questionnaire_response qr JOIN users u ON u.id=qr.user_id GROUP BY u.id ORDER BY avg_score DESC")->fetchAll();
$time = $pdo->query("SELECT DATE(created_at) d, COUNT(*) c FROM questionnaire_response GROUP BY DATE(created_at) ORDER BY d ASC")->fetchAll();

// Looker Studio: provide a view mapping example (documented in README). Here we just show SQL.
$looker_sql = "SELECT qr.id as response_id, u.username, u.role, qr.questionnaire_id, qr.status, qr.score, qr.created_at, qr.reviewed_at, (qr.status='approved') as approved_flag FROM questionnaire_response qr JOIN users u ON u.id=qr.user_id";
?>
<!doctype html><html><head><meta charset="utf-8"><title>Analytics</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/../templates/header.php'; ?>
<section class="content"><div class="container-fluid">
<div class="card"><div class="card-header"><h3>Average Score per User</h3></div>
<div class="card-body">
<table class="table table-sm"><thead><tr><th>User</th><th>Average Score</th><th>N</th></tr></thead><tbody>
<?php foreach ($avg as $r): ?>
<tr><td><?=htmlspecialchars($r['username'])?></td><td><?=number_format((float)$r['avg_score'],2)?></td><td><?=$r['cnt']?></td></tr>
<?php endforeach; ?>
</tbody></table>
</div></div>
<div class="card"><div class="card-header"><h3>Submissions Over Time (daily)</h3></div>
<div class="card-body">
<table class="table table-sm"><thead><tr><th>Date</th><th>Count</th></tr></thead><tbody>
<?php foreach ($time as $r): ?><tr><td><?=$r['d']?></td><td><?=$r['c']?></td></tr><?php endforeach; ?>
</tbody></table>
</div></div>
<div class="card"><div class="card-header"><h3>Looker Studio Fields (SQL)</h3></div>
<div class="card-body"><pre><?=htmlspecialchars($looker_sql)?></pre></div></div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
</div></body></html>