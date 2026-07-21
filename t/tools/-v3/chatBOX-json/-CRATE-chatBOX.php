<?php 
// definers
define("POST_username", $_POST['username'] ?? "");
define("POST_message", $_POST['message'] ?? "");
define("POST_time", time());

function json_payload(){
    return [
    "chat" => [
        "username" => POST_username,
        "message" => POST_message,
        "time" => POST_time
    ]];
}

function json_route(){
    return [];
}


function chatSTORE(){

    //GET YOUR COMMONS!
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE];

    $dom = $a['DOM_SLUG'] ?? (defined('DOM_SLUG') ? DOM_SLUG : 'chat');
    $room = $a['ROOM_SLUG'] ?? (defined('ROOM_SLUG') ? ROOM_SLUG : 'room');
    $dir = defined('ROUTE_TO_LOCALSTORE') ? ROUTE_TO_LOCALSTORE : (echoSONAR . 'd/' . ($a['URI'] ?? 'www') . '/');

    if (function_exists('aleph')) {
        aleph($dir);
    } elseif (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $soperSTACK = $dir . $dom . '-' . $room . '.chat.log.json';
    $chatSTORE = [];
    if (is_file($soperSTACK)) {
        $chatSTORE = json_decode((string) file_get_contents($soperSTACK), true) ?: [];
    }

    $cuid = defined('cUID') ? cUID : ('chat-' . POST_time);
    if (!isset($chatSTORE[POST_time])) {
        $chatSTORE[POST_time] = [
            'USER' => POST_username,
            'MESSAGE' => POST_message,
            'cUID' => $cuid,
            'TIME' => time(),
        ];
    }

    file_put_contents($soperSTACK, json_encode($chatSTORE, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}