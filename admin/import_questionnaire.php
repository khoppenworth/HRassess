<?php
require_once __DIR__ . '/../config.php';
auth_required(['admin']);
csrf_validate();

function create_questionnaire(PDO $pdo, string $title, ?string $xml_id = null, ?string $version = null): int {
  $stmt = $pdo->prepare("INSERT INTO questionnaires (title, xml_id, version) VALUES (?,?,?)");
  $stmt->execute([$title, $xml_id, $version]);
  return (int)$pdo->lastInsertId();
}

function add_item(PDO $pdo, int $qid, ?string $linkId, string $text, string $type): int {
  $stmt = $pdo->prepare("INSERT INTO questionnaire_items (questionnaire_id, link_id, question_text, type) VALUES (?,?,?,?)");
  $stmt->execute([$qid, $linkId, $text, $type]);
  return (int)$pdo->lastInsertId();
}

function add_option(PDO $pdo, int $itemId, string $text, int $isCorrect = 0): void {
  $stmt = $pdo->prepare("INSERT INTO questionnaire_options (item_id, option_text, is_correct) VALUES (?,?,?)");
  $stmt->execute([$itemId, $text, $isCorrect]);
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['xmlFile'])) {
  $fileTmp = $_FILES['xmlFile']['tmp_name'];
  if (!is_uploaded_file($fileTmp)) {
    $err = "File upload error.";
  } else {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_file($fileTmp);
    if ($xml === false) {
      $err = "Invalid XML format.";
    } else {
      $root = $xml->getName();
      try {
        if ($root === 'Questionnaire') {
          // Minimal metadata
          $title = (string)($xml->title ?? 'FHIR Questionnaire');
          $id = (string)($xml->id['value'] ?? '');
          $version = (string)($xml->version['value'] ?? '');
          $qid = create_questionnaire($pdo, $title ?: 'FHIR Questionnaire', $id ?: null, $version ?: null);

          foreach ($xml->item as $item) {
            $text = (string)$item->text;
            $linkId = (string)$item['linkId'];
            $type = (string)$item['type'];
            $itemId = add_item($pdo, $qid, $linkId ?: null, $text ?: '', $type ?: 'string');

            if ($type === 'choice' && isset($item->answerOption)) {
              foreach ($item->answerOption as $opt) {
                // handle valueString/valueCoding.display
                $optText = (string)($opt->valueString ?? $opt->valueCoding->display ?? '');
                if ($optText !== '') add_option($pdo, $itemId, $optText, 0);
              }
            }
          }
          $msg = t('import_success');
        } elseif ($root === 'quiz') {
          $title = 'Moodle Quiz ' . date('Y-m-d H:i:s');
          $qid = create_questionnaire($pdo, $title, null, null);

          foreach ($xml->question as $q) {
            $type = (string)$q['type'];
            $qtext = (string)($q->questiontext->text ?? $q->name->text ?? 'Question');
            $itemId = add_item($pdo, $qid, null, $qtext, $type ?: 'string');

            if (in_array($type, ['multichoice','truefalse','shortanswer'], true)) {
              foreach ($q->answer as $ans) {
                $optText = (string)$ans->text;
                $fraction = (int)($ans['fraction'] ?? 0);
                add_option($pdo, $itemId, $optText, $fraction == 100 ? 1 : 0);
              }
            }
          }
          $msg = t('import_success');
        } else {
          $err = "Unsupported XML. Expect FHIR <Questionnaire> or Moodle <quiz>.";
        }
      } catch (Throwable $e) {
        $err = "Import failed.";
      }
    }
  }
}

include __DIR__ . '/../templates/head.php';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><?=t('import_xml')?></h3></div>
          <div class="card-body">
            <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
            <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <div class="form-group">
                <label><?=t('upload')?></label>
                <input type="file" name="xmlFile" accept=".xml" class="form-control" required>
              </div>
              <button class="btn btn-primary"><?=t('submit')?></button>
            </form>
            <p class="mt-3">
              Supported: FHIR Questionnaire XML (root &lt;Questionnaire&gt;), Moodle quiz XML (root &lt;quiz&gt;).
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../templates/foot.php'; ?>
