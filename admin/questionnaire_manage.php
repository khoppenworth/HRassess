<!-- Placeholder for questionnaire_manage.php -->
<?php
// admin/questionnaire_manage.php
require_once __DIR__ . '/../config.php';
require_admin();
// For brevity, provide minimal UI: create questionnaire and items
$action = $_GET['action'] ?? 'list';
if ($_SERVER['REQUEST_METHOD']==='POST' && $action==='create') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { $err="Invalid CSRF"; }
    else {
        $fhir_id = $_POST['fhir_id']; $title = $_POST['title']; $desc = $_POST['description'];
        $stmt = $pdo->prepare("INSERT INTO questionnaire (fhir_id, title, description, version, status) VALUES (?,?,?,?,?)");
        $stmt->execute([$fhir_id, $title, $desc, '1.0.0', 'active']);
        $msg = "Questionnaire created.";
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && $action==='add_item') {
    $qid = $_POST['questionnaire_id']; $link = $_POST['link_id']; $text = $_POST['text']; $type = $_POST['type']; $required = isset($_POST['required'])?1:0; $opts = $_POST['options_json'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, link_id, text, type, required, options_json) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$qid, $link, $text, $type, $required, $opts]);
    $msg = "Item added.";
}
$questionnaires = $pdo->query("SELECT * FROM questionnaire ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Manage Questionnaires</title><link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper"><?php include __DIR__.'/../templates/header.php'; ?>
<div class="content-wrapper"><section class="content-header"><h1>Questionnaires</h1></section><section class="content"><div class="container-fluid">
<?php if(!empty($msg)) echo "<div class='alert alert-success'>".htmlspecialchars($msg)."</div>"; ?>
<div class="card"><div class="card-header"><h3>Create Questionnaire</h3></div>
<div class="card-body">
<form method="post?action=create"><?=csrf_tag()?>
  <div class="form-group"><label>FHIR id</label><input name="fhir_id" class="form-control" required></div>
  <div class="form-group"><label>Title</label><input name="title" class="form-control" required></div>
  <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
  <button type="submit" name="action" value="create" class="btn btn-primary">Create</button>
</form>
</div></div>

<div class="card"><div class="card-header"><h3>Add Item</h3></div>
<div class="card-body">
<form method="post?action=add_item"><?=csrf_tag()?>
  <div class="form-group"><label>Questionnaire</label><select name="questionnaire_id" class="form-control"><?php foreach($questionnaires as $q) echo "<option value='{$q['id']}'>".htmlspecialchars($q['title'])."</option>"; ?></select></div>
  <div class="form-group"><label>Link Id</label><input name="link_id" class="form-control"></div>
  <div class="form-group"><label>Text</label><input name="text" class="form-control" required></div>
  <div class="form-group"><label>Type</label><select name="type" class="form-control"><option>string</option><option>text</option><option>boolean</option><option>integer</option><option>decimal</option><option>choice</option><option>date</option></select></div>
  <div class="form-group"><label>Options (JSON for choices)</label><textarea name="options_json" class="form-control" placeholder='{"options":[{"code":"1","display":"Yes"}]}'></textarea></div>
  <div class="form-group"><label><input type="checkbox" name="required"> Required</label></div>
  <button type="submit" name="action" value="add_item" class="btn btn-primary">Add Item</button>
</form>
</div></div>

<div class="card"><div class="card-header"><h3>Existing</h3></div><div class="card-body">
<?php foreach($questionnaires as $q): ?>
  <div class="mb-3"><h5><?=htmlspecialchars($q['title'])?></h5><p><?=htmlspecialchars($q['description'])?></p>
    <a href="questionnaire_manage.php?show=<?=$q['id']?>" class="btn btn-sm btn-outline-primary">View items</a>
  </div>
<?php endforeach; ?>
</div></div>

<?php if(isset($_GET['show'])):
  $qid = (int)$_GET['show'];
  $stmt = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ? ORDER BY sort_order");
  $stmt->execute([$qid]); $items = $stmt->fetchAll();
?>
  <div class="card"><div class="card-header"><h3>Items</h3></div><div class="card-body">
    <ul><?php foreach($items as $it) echo "<li>".htmlspecialchars($it['text'])." <small>({$it['type']})</small></li>"; ?></ul>
  </div></div>
<?php endif; ?>

</div></section></div><?php include __DIR__.'/../templates/footer.php'; ?></div><script src="/assets/adminlte/dist/js/adminlte.min.js"></script></body></html>
