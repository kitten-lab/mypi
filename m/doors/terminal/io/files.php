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
h1('files');

getTool('fileKeeper', 'Desk');

closeSky();
