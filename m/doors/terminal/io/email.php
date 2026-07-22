<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'email',
    'Email',
    'classic'
);

openSky('email');
h1('email');

// nothing here yet — the line is quiet on purpose
skylite('<p class="tm-lede tm-lede-quiet">no carrier.</p>');

closeSky();
