<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('icu');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'icu',
    'Terminal ICU',
    'email',
    'Email',
    'classic'
);

openSky('email');
h1('email');

skylite('<p class="tm-lede tm-lede-quiet">no carrier.</p>');
skylite('<p class="tm-lede tm-lede-quiet">postage is for people who still believe in outside.</p>');

closeSky();
