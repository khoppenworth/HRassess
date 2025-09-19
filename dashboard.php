<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';
auth_required(['staff','admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$stmt = $pdo->query("SELECT id, title, description FROM questionnaire ORDER BY created_at DESC");
$questionnaires = $stmt->fetchAll();

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
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
