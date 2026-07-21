<?php
/* navigation — SYS starline; DOM news first */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';
$GLOBALS[BLOCK_ID]['GETS']['topNav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/top-nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
  ['DOM' => 'news'],
  ['DOM' => 'offices'],
  ['DOM' => 'events'],
];

$GLOBALS[BLOCK_ID]['NAV'] = [
  'navSec' => [
    'DOM' => 'news',
    'BUILDING' => 'News',
    'KEY' => 'headlines',
    'ROOMS' => [
      [
        'ROOM' => 'Headlines',
        'KEY' => 'headlines',
      ],
    ],
  ],
];
