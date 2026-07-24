<?php
/**
 * Terminal I/O · START AN IMPORT — load tree-core by core #, then work the log.
 * Active WIP list lives at imports-active (IMPORTS submenu).
 */
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('io');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'io',
    'Terminal IO',
    'import',
    'Import',
    'classic'
);

openSky('import');
h1('start an import');

getTool('logImport', 'Desk');

closeSky();
