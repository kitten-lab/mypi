<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('ab');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'ab',
    'Terminal AB',
    'email',
    'Email',
    'classic'
);

openSky('email');
h1('email');

skylite('<p class="tm-lede tm-lede-quiet">no carrier.</p>');
skylite('<p class="tm-lede tm-lede-quiet">the inbox is a red room with the lights off.</p>');

closeSky();
