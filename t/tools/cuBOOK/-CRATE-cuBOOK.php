<?php 

function json_payload(){
    return [
    "guestCU" => [
        "agent" => $_POST['USER'],
        "topic" => $_POST['MESSAGE'],
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

