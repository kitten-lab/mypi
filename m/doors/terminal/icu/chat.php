<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('icu');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'icu',
    'Terminal ICU',
    'chat',
    'Chat',
    'classic'
);

openSky('chat');
h1('chat');

skylite('<p class="tm-lede tm-lede-quiet">no open channels.</p>');
skylite('<p class="tm-lede tm-lede-quiet">the watchers do not small-talk on this line.</p>');

closeSky();
