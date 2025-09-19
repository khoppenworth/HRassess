<?php
require_once __DIR__ . '/../config.php';
auth_required(['admin']);
csrf_validate();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
  $stmt = $pdo->prepare("INSERT INTO questionnaires (title) VALUES (?)");
  $stmt->execute([trim($_POST['title'])]);
  $msg = 'Questionnaire created.';
}

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM questionnaires WHERE id = ?")->execute([$id]);
  header('Location: /admin/questionnaires.php'); exit;
}

$rows = $pdo->query("SELECT * FROM questionnaires ORDER BY id DESC")->fetchAll();
include __DIR__ . '/../templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Questionnaires</h3>
            <a class="btn btn-secondary btn-sm" href="/admin/import_questionnaire.php"><?=t('import_xml')?></a>
          </div>
          <div class="card-body">
            <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
            <form method="post" class="mb-3">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <div class="form-row">
                <div class="col"><input name="title" class="form-control" placeholder="Title" required></div>
                <div class="col"><button class="btn btn-primary">Create</button></div>
              </div>
            </form>
            <table class="table table-bordered">
              <thead><tr><th>ID</th><th>Title</th><th>Created</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?=$r['id']?></td>
                  <td><?=htmlspecialchars($r['title'])?></td>
                  <td><?=$r['created_at']?></td>
                  <td>
                    <a class="btn btn-danger btn-sm" href="/admin/questionnaires.php?delete=<?=$r['id']?>" onclick="return confirm('Delete questionnaire?')">Delete</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/foot.php'; ?>
