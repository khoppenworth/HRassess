<!-- Placeholder for submit_assessment.php -->

<?php
// submit_assessment.php
require_once 'config.php';
require_login();

// choose active questionnaire (simple: first active)
$stmt = $pdo->query("SELECT * FROM questionnaire WHERE status='active' LIMIT 1");
$q = $stmt->fetch();
if (!$q) {
    die("No active questionnaire found. Admin must create one.");
}

// load items
$stmt = $pdo->prepare("SELECT * FROM questionnaire_item WHERE questionnaire_id = ? ORDER BY sort_order ASC");
$stmt->execute([$q['id']]);
$items = $stmt->fetchAll();

$err = $msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { $err = "Invalid CSRF"; }
    else {
        // create new questionnaire_response
        $fhir_id = 'qr-'.bin2hex(random_bytes(6));
        $user_id = current_user_id();
        $status = 'completed';
        $authored = date('Y-m-d H:i:s');
        $region = $_POST['region'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO questionnaire_response (fhir_id, questionnaire_id, user_id, status, authored, region) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fhir_id, $q['id'], $user_id, $status, $authored, $region]);
        $qr_id = $pdo->lastInsertId();

        $ins = $pdo->prepare("INSERT INTO questionnaire_response_item (questionnaire_response_id, questionnaire_item_id, link_id, answer_text, answer_boolean, answer_integer, answer_decimal, answer_date, answer_datetime, answer_json, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $i=0;
        foreach ($items as $it) {
            $qid = $it['id'];
            $link = $it['link_id'] ?: $qid;
            $val = $_POST['q_'.$qid] ?? null;

            $a_text = null; $a_bool = null; $a_int=null; $a_dec=null; $a_date=null; $a_dt=null; $a_json=null;
            switch($it['type']) {
                case 'boolean': $a_bool = $val ? 1 : 0; break;
                case 'integer': $a_int = is_numeric($val)?(int)$val:null; break;
                case 'decimal': $a_dec = is_numeric($val)?(float)$val:null; break;
                case 'date': $a_date = $val; break;
                case 'dateTime': $a_dt = $val; break;
                case 'choice': $a_json = json_encode(['code'=>$val]); break;
                default: $a_text = $val; break;
            }
            $ins->execute([$qr_id, $qid, $link, $a_text, $a_bool, $a_int, $a_dec, $a_date, $a_dt, $a_json, $i++]);
        }
        $msg = "Assessment submitted.";
    }
}

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Submit Assessment</title>
<link rel="stylesheet" href="/assets/adminlte/dist/css/adminlte.min.css"></head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'templates/header.php'; ?>
  <div class="content-wrapper">
    <section class="content-header"><h1>Submit Assessment</h1></section>
    <section class="content">
      <div class="container-fluid">
        <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
        <?php if($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>
        <form method="post">
          <?=csrf_tag()?>
          <input type="hidden" name="region" value="<?=htmlspecialchars($_SESSION['region'] ?? '')?>">
          <div class="card">
            <div class="card-header"><h3><?=$q['title']?></h3></div>
            <div class="card-body">
              <?php foreach($items as $it): ?>
                <div class="form-group">
                  <label><?=htmlspecialchars($it['text'])?> <?php if($it['required']):?><span class="text-danger">*</span><?php endif;?></label>
                  <?php
                    $name = 'q_'.$it['id'];
                    if ($it['type']==='boolean') {
                      echo "<select class='form-control' name='".htmlspecialchars($name)."'><option value=''>Select</option><option value='1'>Yes</option><option value='0'>No</option></select>";
                    } elseif ($it['type']==='choice' && $it['options_json']) {
                      $opt = json_decode($it['options_json'], true);
                      echo "<select class='form-control' name='".htmlspecialchars($name)."'><option value=''>Select</option>";
                      foreach($opt['options'] as $o) {
                        echo "<option value='".htmlspecialchars($o['code'])."'>".htmlspecialchars($o['display'])."</option>";
                      }
                      echo "</select>";
                    } elseif ($it['type']==='integer') echo "<input type='number' class='form-control' name='".htmlspecialchars($name)."'>";
                    elseif ($it['type']==='date') echo "<input type='date' class='form-control' name='".htmlspecialchars($name)."'>";
                    else echo "<input type='text' class='form-control' name='".htmlspecialchars($name)."'>";
                  ?>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="card-footer"><button class="btn btn-primary" type="submit">Submit</button></div>
          </div>
        </form>
      </div>
    </section>
  </div>
  <?php include 'templates/footer.php'; ?>
</div>
<script src="/assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
