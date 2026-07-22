<?php
/**
 * Terminal I/O · IMPORT — protopass bay (load tree-core by imposition number).
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
h1('import');

getTool('logImport', 'Desk');

closeSky();
