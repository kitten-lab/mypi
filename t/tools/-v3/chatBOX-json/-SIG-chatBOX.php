<?php 
define("SHADOW_TOGGLE", false);


$GLOBALS['TOOL'] = [
    "SHADOWENVO" => SHADOW_TOGGLE,
    "NAME" => "chatBOX",
    "FUNCTION" => "chatroom",
    "CATALOG_SLUG" => "chatBOX chat",
    "TYPE" => "chat",
    "VERSION" => 4,
    ];

    
global $SIGFIG;
$SIGFIG['chatBOX'] = [
    "ChatBox" => [
        'classic' => [
          "username" => "Character",
          "username_pl" => "~ any name will do ~",
          "message" => "Message Body",
          "message_pl" => "~ body of your message ~"
        ]
    ]
];