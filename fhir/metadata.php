<?php
header('Content-Type: application/json');
echo json_encode([
 'resourceType'=>'CapabilityStatement',
 'status'=>'active',
 'date'=>date(DATE_ATOM),
 'publisher'=>'EPSS',
 'kind'=>'instance',
 'fhirVersion'=>'4.0.1',
 'format'=>['json'],
 'rest'=>[['mode'=>'server','resource'=>[['type'=>'Questionnaire'],['type'=>'QuestionnaireResponse']]]]
]);
?>