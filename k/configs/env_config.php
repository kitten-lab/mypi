<?php
// Machine name for local dev (was ROSEWOOD8 on old laptop; this box is COMMANDCENTER9).
// Online/public is optional — this tool is for you first.
global $ENV;
$ENV = "COMMANDCENTER9";
date_default_timezone_set("America/New_York");

// SET ROUTE LINE JUNCTION POINTS 
$skyJUNCTS = [ "a", "b", "c", "d", "k", "m", "t", "dir", 'map' ];

foreach ($skyJUNCTS as $skyJUNCT){
    SKY_JUNCTION($skyJUNCT);
}

/*
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
*/

?>