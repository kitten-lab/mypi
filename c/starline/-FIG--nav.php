<?php
/* navigation — SYS starline; report DOMs parallel d/_CHESTER _CHARLIE _SATORA */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';
$GLOBALS[BLOCK_ID]['GETS']['topNav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/top-nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
  ['DOM' => 'news'],
  ['DOM' => 'chester'],
  ['DOM' => 'charlie'],
  ['DOM' => 'satora'],
  ['DOM' => 'offices'],
  ['DOM' => 'events'],
];

$GLOBALS[BLOCK_ID]['NAV'] = [
  'navSec' => [
    'DOM' => 'news',
    'BUILDING' => 'News',
    'KEY' => 'headlines',
    'ROOMS' => [
      ['ROOM' => 'Headlines', 'KEY' => 'headlines'],
    ],
  ],
];
