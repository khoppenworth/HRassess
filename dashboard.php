<?php
require_once __DIR__ . '/config.php';
auth_required();
include __DIR__ . '/templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h3>Welcome, <?=htmlspecialchars($_SESSION['username'])?></h3>
            <p>This is your dashboard.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/templates/foot.php'; ?>
