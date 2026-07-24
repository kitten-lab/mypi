<?php
/**
 * Terminal I/O · ACTIVE IMPORTS — WIP list (click → start bay with face loaded).
 */
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'imports-active',
    'Active Imports',
    'classic'
);

openSky('imports-active');
h1('active imports');

getTool('logImport', 'Active');

closeSky();
