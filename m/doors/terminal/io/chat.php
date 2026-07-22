<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'chat',
    'Chat',
    'classic'
);

openSky('chat');
h1('chat');

getTool('chatBOX', 'ChatRoom');

closeSky();
