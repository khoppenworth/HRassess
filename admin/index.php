<?php
require_once __DIR__ . '/../config.php';
auth_required(['admin']);
include __DIR__ . '/../templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h3>Admin</h3>
            <ul>
              <li><a href="/admin/users.php"><?=t('users')?></a></li>
              <li><a href="/admin/questionnaires.php"><?=t('questionnaires')?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/foot.php'; ?>
