<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('ab');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'ab',
    'Terminal AB',
    'dossier',
    'Dossier',
    'classic'
);

openSky('dossier');

getTool('dossierDesk', 'Desk');

closeSky();
