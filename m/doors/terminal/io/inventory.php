<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'inventory',
    'invent-0rium',
    'classic'
);

openSky('invent-0rium');
/* Display name drifts; room slug inventory is stable. */

getTool('inventOry', 'Desk');

closeSky();
