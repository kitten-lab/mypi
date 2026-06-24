<?php 

function json_payload(){
    return [
    "post" => [
        "agent" => $_POST['agent'],
        "topic" => $_POST['POST__TIMBER_TOPIC'],
        "content" => $_POST['POST__TIMBER_LEAF'],
    ]];
}

function json_route(){ $SITE = $GLOBALS['SITE'];

    return [
    "from" => [
        "mod" => $_POST['POST__REPORTER'] ?? $GLOBALS[$SITE]['MOD_SLUG'],
    ],
    "to" => [
        "mod" => $GLOBALS['TO']['MOD_SLUG'] ?? $GLOBALS[$SITE]['MOD_SLUG'],
        "room" => $GLOBALS['TO']['ROOM_SLUG'] ?? $GLOBALS[$SITE]['ROOM_SLUG'],
        "dom" => $GLOBALS['TO']['DOM_SLUG'] ?? $GLOBALS[$SITE]['DOM_SLUG'],
        "sys" => $GLOBALS['TO']['SYS_SLUG'] ?? $GLOBALS[$SITE]['SYS_SLUG'],
    ]];
}
