<?php
function t(string $key): string {
  $lang = $_SESSION['lang'] ?? 'en';
  $file = __DIR__ . "/../locale/{$lang}.json";
  if (!file_exists($file)) $file = __DIR__ . "/../locale/en.json";
  $data = json_decode(file_get_contents($file), true) ?: [];
  return $data[$key] ?? $key;
}
