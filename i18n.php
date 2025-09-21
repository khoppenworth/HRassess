<?php
function load_lang($lang='en'){
  $file = __DIR__ . "/lang/{$lang}.json";
  if (!file_exists($file)) $file = __DIR__ . "/lang/en.json";
  $data = json_decode(file_get_contents($file), true);
  return is_array($data) ? $data : [];
}
?>