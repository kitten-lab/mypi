<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('icu');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'icu',
    'Terminal ICU',
    'shots',
    'Shots',
    'classic'
);

openSky('shots');

getTool('shotDesk', 'Desk');

closeSky();
