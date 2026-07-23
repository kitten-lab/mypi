<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('ab');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'ab',
    'Terminal AB',
    'chat',
    'Chat',
    'classic'
);

openSky('chat');
h1('chat');

// empty for now — channel not opened
skylite('<p class="tm-lede tm-lede-quiet">no open channels.</p>');
skylite('<p class="tm-lede tm-lede-quiet">if someone is typing, you are not meant to hear it yet.</p>');

closeSky();
