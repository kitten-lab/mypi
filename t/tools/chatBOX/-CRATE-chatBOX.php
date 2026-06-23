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

          $soperSTACK = ROUTE_TO_LOCALSTORE . $a['DOM_SLUG'] . '-' . $a['ROOM_SLUG'] . '.chat.log.json';
          $chatSTORE = json_decode(file_get_contents($soperSTACK), true);

        if (!$chatSTORE){
            $chatSTORE = [];
        }

        if (!isset($chatSTORE[POST_time])) {
            $chatSTORE[POST_time] = [
                'USER' => POST_username,
                'MESSAGE' => POST_message,
                  'cUID' => cUID,
            ];
        }


    file_put_contents($soperSTACK, json_encode($chatSTORE, JSON_PRETTY_PRINT));
    
}