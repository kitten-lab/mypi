<?php
/* Master Mailroom — not a terminal · Chester network · Charlie */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
    ['DOM' => 'floor'],
];

$GLOBALS[BLOCK_ID]['NAV'] = [
    'navSec' => [
        'DOM' => 'floor',
        'BUILDING' => 'Floor',
        'KEY' => 'sort',
        'ROOMS' => [
            ['ROOM' => 'sort', 'KEY' => 'sort'],
        ],
    ],
];
?>
