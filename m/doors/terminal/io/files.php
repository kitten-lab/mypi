<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'files',
    'Files',
    'classic'
);

openSky('files');
/* Room name lives in the terminal tabline only — no second FILES header in the desk. */

getTool('fileKeeper', 'Desk');

closeSky();
