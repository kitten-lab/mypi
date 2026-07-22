<?php
/* navigation — SYS book; shelf also scans m/doors/book live in asSys/nav.php */

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';
$GLOBALS[BLOCK_ID]['GETS']['topNav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php';

$GLOBALS[BLOCK_ID]['tDOM'] = [
  ['DOM' => 'fragments'],
  ['DOM' => 'terminal_girls'],
  ['DOM' => 'scenes'],
  ['DOM' => 'tavern'],
  ['DOM' => 'hidden'],
];

// Hand catalog kept for tools that read NAV; sidebar prefers disk scan.
$GLOBALS[BLOCK_ID]['NAV'] = [
  'navSec' => [
    'DOM' => 'fragments',
    'BUILDING' => 'Fragment Sheets',
    'KEY' => 'connection',
    'ROOMS' => [
      ['ROOM' => 'connection', 'KEY' => 'connection'],
      ['ROOM' => 'freedom', 'KEY' => 'freedom'],
      ['ROOM' => 'intentions', 'KEY' => 'intentions'],
      ['ROOM' => 'misery', 'KEY' => 'misery'],
      ['ROOM' => 'machine-mems', 'KEY' => 'machine-mems'],
      ['ROOM' => 'god-in-the-machine', 'KEY' => 'god-in-the-machine'],
      ['ROOM' => 'big-story', 'KEY' => 'big-story'],
    ],
  ],
];
?>
