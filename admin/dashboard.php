<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../i18n.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');

$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalQ = (int)$pdo->query("SELECT COUNT(*) FROM questionnaire")->fetchColumn();
$totalResponses = (int)$pdo->query("SELECT COUNT(*) FROM questionnaire_response")->fetchColumn();

include __DIR__ . '/../templates/header.php';
?>
<section class="content">
  <div class="container-fluid">
    <h2><?= htmlspecialchars($t['admin_dashboard']) ?></h2>
    <div class="row">
      <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
          <div class="inner"><h3><?=$totalUsers?></h3><p><?= htmlspecialchars($t['users']) ?></p></div>
          <div class="icon"><i class="fas fa-users"></i></div>
          <a href="/admin/users.php" class="small-box-footer"><?= htmlspecialchars($t['manage_users']) ?> <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
          <div class="inner"><h3><?=$totalQ?></h3><p><?= htmlspecialchars($t['questionnaires']) ?></p></div>
          <div class="icon"><i class="fas fa-file-alt"></i></div>
          <a href="/admin/questionnaire_manage.php" class="small-box-footer"><?= htmlspecialchars($t['manage_questionnaires']) ?> <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
          <div class="inner"><h3><?=$totalResponses?></h3><p>Responses</p></div>
          <div class="icon"><i class="fas fa-check-circle"></i></div>
          <a href="/admin/export.php" class="small-box-footer"><?= htmlspecialchars($t['export_csv']) ?> <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
