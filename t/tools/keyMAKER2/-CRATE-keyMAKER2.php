<?php 

function json_payload(){
    return [
    "keyMaker" => [
        "openSky" => $_POST['openSky']
    ]];
}

function json_route(){
$SITE = $GLOBALS['SITE'];
    return [
        "from" => [
            "agent" => $_POST['USER'],
        ],
        "to" => [
            "sys" => $GLOBALS[$SITE]['SYS_SLUG'],
            "dom" => $GLOBALS[$SITE]['DOM_SLUG'],
            "mod" => $GLOBALS[$SITE]['MOD_SLUG'],
            "key" => $GLOBALS[$SITE]['ROOM_SLUG'],
        ]
    ];
}

