<?php
/* Terminal network — DOM = station id; rooms shared grammar */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
    ['DOM' => 'base'],
    ['DOM' => 'io'],
    ['DOM' => 'ab'],
    ['DOM' => 'icu'],
    ['DOM' => 'rx'],
    // future: jx, cu, …
];

$GLOBALS[BLOCK_ID]['NAV'] = [
    'navSec' => [
        'DOM' => 'base',
        'BUILDING' => 'Base station',
        'KEY' => 'login',
        'ROOMS' => [
            ['ROOM' => 'login', 'KEY' => 'login'],
        ],
    ],
];
?>
