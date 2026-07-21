<?php
define('SHADOW_TOGGLE', false);

$GLOBALS['TOOL'] = [
    'SHADOWENVO' => SHADOW_TOGGLE,
    'NAME' => 'chatBOX',
    'FUNCTION' => 'chatroom',
    'CATALOG_SLUG' => 'chatBOX chat line',
    'TYPE' => 'chat',
    'VERSION' => 6,
];

global $SIGFIG;
$SIGFIG['chatBOX'] = [
    'ChatBox' => [
        'classic' => [
            'username' => 'Character',
            'username_pl' => '~ any name will do ~',
            'message' => 'Message Body',
            'message_pl' => '~ body of your message ~',
            'chat_session' => 'Session id',
            'chat_session_pl' => 'live',
            'chat_session_label' => 'Session title',
            'chat_session_label_pl' => 'optional label for this hangout',
        ],
        'early-web' => [
            'username' => 'Nick',
            'username_pl' => 'your nick',
            'message' => 'Say something',
            'message_pl' => '…',
            'chat_session' => 'Session',
            'chat_session_pl' => 'live',
            'chat_session_label' => 'Room title',
            'chat_session_label_pl' => '',
        ],
        'skyline-standard' => [
            'username' => 'Agent',
            'username_pl' => 'callsign',
            'message' => 'Transmission',
            'message_pl' => '…',
            'chat_session' => 'Channel',
            'chat_session_pl' => 'live',
            'chat_session_label' => 'Channel title',
            'chat_session_label_pl' => '',
        ],
    ],
    'ChatRoom' => [
        'classic' => [],
        'early-web' => [],
        'skyline-standard' => [],
    ],
];
