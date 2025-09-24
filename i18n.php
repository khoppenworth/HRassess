<?php
function load_lang($lang='en'){
  $p=__DIR__.'/lang/'.$lang.'.json';
  if(!file_exists($p)) $p=__DIR__.'/lang/en.json';
  return json_decode(file_get_contents($p),true)??[];
}
?>