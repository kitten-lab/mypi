<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('ab');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'ab',
    'Terminal AB',
    'files',
    'Files',
    'classic'
);

openSky('files');
/* Room name lives in the terminal tabline only. */

getTool('fileKeeper', 'Desk');

closeSky();
