<?php
require_once 'auth_check.php';
$ENV = "ROSEWOOD8";
date_default_timezone_set("America/New_York");

// SET ROUTE LINE JUNCTION POINTS 
$skyJUNCTS = [ "a", "b", "c", "d", "k", "m", "t", "dir", 'map' ];

foreach ($skyJUNCTS as $skyJUNCT){
    SKY_JUNCTION($skyJUNCT);
}

$MATERIAL = [
    "TYPE" => [],
    "SOURCE" => [
        "NAME" => [],
        "ID" => [],
        "CREATED" => [],
        "LAST_MODIFIED" => []
    ],
    "REFS" => [],
    "DETAILS" => [],
    "USER" => "me",
    "ASSISTANT" => "chatGPT"
];

$DEMO = [
    'USER' => "pink",
    'ASSISTANT' => "blue"
];

?>