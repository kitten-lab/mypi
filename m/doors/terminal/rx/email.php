<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('rx');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'rx',
    'Terminal RX',
    'email',
    'E-Mail',
    'classic'
);

openSky('email');
h1('e-mail');

skylite('<p class="tm-lede tm-lede-quiet">no carrier on this station yet.</p>');
skylite('<p class="tm-lede tm-lede-quiet">prophecies will land in files · codes live in VEN.</p>');

closeSky();
