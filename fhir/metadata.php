<?php
require_once __DIR__ . '/utils.php';
json_response([
  "resourceType" => "CapabilityStatement",
  "status" => "active",
  "date" => date('c'),
  "kind" => "instance",
  "software" => ["name" => "EPSS Self-Assessment", "version" => "1.2.0"],
  "format" => ["json"],
  "rest" => [[
    "mode" => "server",
    "resource" => [
      ["type" => "Questionnaire", "interaction" => [["code"=>"read"],["code"=>"search-type"]]],
      ["type" => "QuestionnaireResponse", "interaction" => [["code"=>"read"],["code"=>"create"],["code"=>"search-type"]]]
    ]
  ]]
]);
