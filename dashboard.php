<!-- Placeholder for dashboard.php -->
<?php
// dashboard.php
require_once 'config.php';
require_login();

// count responses for user's region or overall if admin
if (current_user_role()==='admin') {
  $stmt = $pdo->query("SELECT COUNT(*) as c FROM questionnaire_response");
  $total = $stmt->fetchColumn();
} else {
  $uid = current_user_id();
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM questionnaire_response WHERE user_id = ?");
  $stmt->execute([$uid]);
  $total = $stmt->fetchColumn();
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - EPSS</title>
  <link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'templates/header.php'; ?>
  <div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Dashboard</h1></div></section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner"><h3><?=htmlspecialchars($total)?></h3><p>Assessments</p></div>
              <div class="icon"><i class="fas fa-file-alt"></i></div>
              <a href="submit_assessment.php" class="small-box-footer">Submit assessment <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3 class="card-title">Recent Assessments</h3></div>
          <div class="card-body">
            <table class="table table-bordered">
              <thead><tr><th>ID</th><th>Questionnaire</th><th>User</th><th>Authored</th><th>Status</th></tr></thead>
              <tbody>
              <?php
              $stmt = $pdo->prepare("SELECT qr.*, q.title, u.username FROM questionnaire_response qr LEFT JOIN questionnaire q ON qr.questionnaire_id=q.id LEFT JOIN users u ON qr.user_id=u.id ORDER BY qr.authored DESC LIMIT 10");
              $stmt->execute();
              while ($r = $stmt->fetch()) {
                  echo "<tr><td>".htmlspecialchars($r['fhir_id'])."</td><td>".htmlspecialchars($r['title'])."</td><td>".htmlspecialchars($r['username'])."</td><td>".htmlspecialchars($r['authored'])."</td><td>".htmlspecialchars($r['status'])."</td></tr>";
              }
              ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
  </div>
  <?php include 'templates/footer.php'; ?>
</div>
<script src="/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
