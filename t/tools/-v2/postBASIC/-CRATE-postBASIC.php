<?php 

function json_payload(){
    return [
    "post" => [
        "agent" => $_POST['agent'],
        "topic" => $_POST['POST__TIMBER_TOPIC'],
        "content" => $_POST['POST__TIMBER_LEAF'],
    ]];
}

function json_route(){
$SITE = $GLOBALS['SITE'];
    return [];
}

