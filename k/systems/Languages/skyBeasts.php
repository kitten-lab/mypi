<?php 

function callSkyBeasts(){
  return [
  [ "name" => "SKY__AUTH", "type" => "php" ],
  [ "name" => "SKY_JUNCTION", "type" => "php" ],
  [ "name" => "kittenCaller", "type" => "php" ],
  [ "name" => "chestersToyBox", "type" => "php" ],
  [ "name" => "consoleNIMs", "type" => "php" ],
  [ "name" => "setGET", "type" => "php" ],
  [ "name" => "costumeChange", "type" => "php" ],
  [ "name" => "getROOMKEY", "type" => "php" ],
  [ "name" => "getSIGFIG", "type" => "php" ],
  [ "name" => "getFILLER", "type" => "php" ],
  [ "name" => "getDECOR", "type" => "php" ],
  [ "name" => "carlsToolBox", "type" => "php" ],
  [ "name" => "displayToy", "type" => "php" ],
  [ "name" => "romStage", "type" => "php" ],
  ];
}


$skyLINES = callSkyBeasts();

foreach ($skyLINES as $skyLINE) {
  $echoLocate = __DIR__ . '/skyBeasts/' . $skyLINE['name'] . ".skyLINE." . $skyLINE['type'];
  if (!file_exists($echoLocate)){
    $failedload[] = $skyLINE['name'];
  } else {
    include $echoLocate;
    $loaded[] = $skyLINE['name'];
  }
}

Console_Log("Loading skyLINES.....","background-color:black;color:white;padding:20px;");

$confirm_message = count($loaded ?? 0) . " of " . count($skyLINES) . " skyLINES successfully pulled: " . implode(", ", $loaded ?? 0);
if (!empty($failedload)){
    $fail_message = count($failedload) . " skyLINES failed pulled: " . implode(", ", $failedload);
    KDE_Error_Logger("!!! LANGUAGE IMPORT ERROR, PLEASE REPORT!!! MISSING", $fail_message);
}

Console_Log($confirm_message);

