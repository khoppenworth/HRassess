<?php
require_once __DIR__.'/../config.php';
auth_required(['admin']);
$t = load_lang($_SESSION['lang'] ?? 'en');
$msg='';

// Create questionnaire
if (isset($_POST['create_q'])) { csrf_check();
  $stm=$pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?,?)");
  $stm->execute([$_POST['title'], $_POST['description']]);
  $msg='Questionnaire created';
}
// Add section
if (isset($_POST['create_s'])) { csrf_check();
  $stm=$pdo->prepare("INSERT INTO questionnaire_section (questionnaire_id,title,description,order_index) VALUES (?,?,?,?)");
  $stm->execute([$_POST['qid'], $_POST['title'], $_POST['description'], (int)$_POST['order_index']]);
  $msg='Section created';
}
// Add item
if (isset($_POST['create_i'])) { csrf_check();
  $stm=$pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id,section_id,linkId,text,type,order_index) VALUES (?,?,?,?,?,?)");
  $sec = $_POST['section_id'] ?: null;
  $stm->execute([$_POST['qid'], $sec, $_POST['linkId'], $_POST['text'], $_POST['type'], (int)$_POST['order_index']]);
  $msg='Item created';
}

// Import FHIR JSON/XML
if (isset($_POST['import'])) { csrf_check();
  if (!empty($_FILES['file']['tmp_name'])) {
    $raw = file_get_contents($_FILES['file']['tmp_name']);
    $data = null;
    if (stripos($_FILES['file']['name'], '.json') !== false) {
        $data = json_decode($raw, true);
    } else {
        // very basic XML to array for Questionnaire
        $xml = simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $data = json_decode($json, true);
    }
    if ($data) {
        // Expect a Questionnaire with item[] or a Bundle of Questionnaire
        $qs = [];
        if (($data['resourceType'] ?? '') === 'Bundle') {
            foreach ($data['entry'] ?? [] as $e) {
                if (($e['resource']['resourceType'] ?? '') === 'Questionnaire') $qs[] = $e['resource'];
            }
        } elseif (($data['resourceType'] ?? '') === 'Questionnaire') {
            $qs[] = $data;
        }
        foreach ($qs as $qq) {
            $stm=$pdo->prepare("INSERT INTO questionnaire (title, description) VALUES (?,?)");
            $stm->execute([$qq['title'] ?? 'FHIR Questionnaire', $qq['description'] ?? null]);
            $qid = (int)$pdo->lastInsertId();
            $order = 1;
            foreach (($qq['item'] ?? []) as $it) {
                $type = $it['type'] ?? 'text';
                $text = $it['text'] ?? ($it['linkId'] ?? 'item');
                $stm2 = $pdo->prepare("INSERT INTO questionnaire_item (questionnaire_id, section_id, linkId, text, type, order_index) VALUES (?,?,?,?,?,?)");
                $stm2->execute([$qid, null, $it['linkId'] ?? ('i'.$order), $text, in_array($type,['boolean','text','textarea'])?$type:'text', $order]);
                $order++;
            }
        }
        $msg = 'FHIR import complete';
    } else $msg = 'Invalid file';
  } else $msg='No file';
}

$qs = $pdo->query("SELECT * FROM questionnaire ORDER BY id DESC")->fetchAll();
$sections = $pdo->query("SELECT * FROM questionnaire_section ORDER BY questionnaire_id, order_index")->fetchAll();
$items = $pdo->query("SELECT * FROM questionnaire_item ORDER BY questionnaire_id, order_index")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Questionnaires</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="/assets/css/styles.css"></head>
<body class="hold-transition sidebar-mini"><div class="wrapper">
<?php include __DIR__.'/../templates/header.php'; ?>
<section class="content"><div class="container-fluid">
<?php if ($msg): ?><div class="alert alert-info"><?=$msg?></div><?php endif; ?>

<div class="card"><div class="card-header"><h3>Create Questionnaire</h3></div><div class="card-body">
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<input class="form-control mb-2" name="title" placeholder="Title" required>
<textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
<button class="btn btn-primary" name="create_q">Create</button>
</form></div></div>

<div class="card"><div class="card-header"><h3>Add Section</h3></div><div class="card-body">
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<select name="qid" class="form-control mb-2">
<?php foreach ($qs as $q): ?><option value="<?=$q['id']?>"><?=$q['title']?></option><?php endforeach; ?>
</select>
<input class="form-control mb-2" name="title" placeholder="Title" required>
<textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
<input class="form-control mb-2" name="order_index" type="number" value="1">
<button class="btn btn-secondary" name="create_s">Add Section</button>
</form></div></div>

<div class="card"><div class="card-header"><h3>Add Item</h3></div><div class="card-body">
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<select name="qid" class="form-control mb-2">
<?php foreach ($qs as $q): ?><option value="<?=$q['id']?>"><?=$q['title']?></option><?php endforeach; ?>
</select>
<select name="section_id" class="form-control mb-2">
<option value="">(no section)</option>
<?php foreach ($sections as $s): ?><option value="<?=$s['id']?>">Q<?=$s['questionnaire_id']?> - <?=$s['title']?></option><?php endforeach; ?>
</select>
<input class="form-control mb-2" name="linkId" placeholder="linkId" required>
<input class="form-control mb-2" name="text" placeholder="Item text" required>
<select name="type" class="form-control mb-2"><option>text</option><option>textarea</option><option>boolean</option></select>
<input class="form-control mb-2" name="order_index" type="number" value="1">
<button class="btn btn-secondary" name="create_i">Add Item</button>
</form></div></div>

<div class="card"><div class="card-header"><h3>FHIR Import</h3></div><div class="card-body">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?=csrf_token()?>">
<input type="file" name="file" class="form-control mb-2" required>
<button class="btn btn-info" name="import">Import</button>
</form>
<p>Download XML template: <a href="/samples/sample_questionnaire_template.xml">sample_questionnaire_template.xml</a></p>
</div></div>

</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
</div></body></html>