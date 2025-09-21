<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';
auth_required(['staff','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$stmt = $pdo->query("SELECT id, title, description FROM questionnaire ORDER BY created_at DESC");
$questionnaires = $stmt->fetchAll();

$my = $pdo->prepare("SELECT r.id, q.title, r.created_at, r.status FROM questionnaire_response r JOIN questionnaire q ON q.id = r.questionnaire_id WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 20");
$my->execute([$_SESSION['user_id']]);
$history = $my->fetchAll();

include __DIR__ . '/templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['dashboard']) ?></h2>
    <div class="row">
      <?php foreach ($questionnaires as $q): ?>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?=htmlspecialchars($q['title'])?></h5>
              <p class="card-text"><?=htmlspecialchars($q['description'])?></p>
              <a class="btn btn-primary" href="/submit_assessment.php?qid=<?=$q['id']?>"><?= htmlspecialchars($t['start']) ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card mt-3">
      <div class="card-header"><h3 class="card-title"><?= htmlspecialchars($t['submission_history']) ?></h3></div>
      <div class="card-body">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th><?= htmlspecialchars($t['questionnaires']) ?></th><th><?= htmlspecialchars($t['status']) ?></th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach($history as $h): ?>
              <tr>
                <td><?=$h['id']?></td>
                <td><?=htmlspecialchars($h['title'])?></td>
                <td><?=htmlspecialchars($t[$h['status']] ?? $h['status'])?></td>
                <td><?=$h['created_at']?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
