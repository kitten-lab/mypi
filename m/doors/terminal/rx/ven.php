<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('rx');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'rx',
    'Terminal RX',
    'ven',
    'VEN',
    'classic'
);

openSky('ven');

getTool('venDesk', 'Desk');

closeSky();
