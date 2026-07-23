<?php
/* Mythleak — public paper · NEWS is home */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
    ['DOM' => 'news'],
];

$GLOBALS[BLOCK_ID]['NAV'] = [
    'navSec' => [
        'DOM' => 'news',
        'BUILDING' => 'THE JUICE LINE',
        'KEY' => 'headlines',
        'ROOMS' => [
            ['ROOM' => 'headlines', 'KEY' => 'headlines'],
            ['ROOM' => 'write', 'KEY' => 'write'],
            ['ROOM' => 'article', 'KEY' => 'article'],
        ],
    ],
];
?>
