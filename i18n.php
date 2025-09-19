<?php
function load_lang($lang = 'en') {
    $file = __DIR__ . "/lang/{$lang}.json";
    if (!file_exists($file)) {
        $file = __DIR__ . "/lang/en.json"; // fallback
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if (!is_array($data)) $data = [];
    return $data;
}
?>