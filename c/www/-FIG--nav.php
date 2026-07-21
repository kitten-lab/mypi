<?php
/**
 * DEMO/WWW navigation — restored tDOM + rooms from archive.
 * Sources: c/--archive/DEMO/WWW/-FIG--nav.php + m/doors/--archive/DEMO/WWW/*
 */

// Master WWW shell is a browser window — do NOT inject sideNav into the chrome.
// tDOM/NAV stay for routing + pocket menu only; pages open by path / address bar.

$GLOBALS[BLOCK_ID]['tDOM'] = [
    ['DOM' => 'lab'],
    ['DOM' => 'danyi'],
    ['DOM' => 'find'],
    ['DOM' => 'public'],
    ['DOM' => 'EXE-708'],
    ['DOM' => 'games'],
    ['DOM' => 'roms'],
    ['DOM' => 'private'],
];

$GLOBALS[BLOCK_ID]['NAV'] = [
    [
        'DOM' => 'lab',
        'BUILDING' => 'Tool Lab (tests)',
        'KEY' => 'home',
        'ROOMS' => [
            ['ROOM' => 'Lab home', 'KEY' => 'home'],
            ['ROOM' => 'cuBOOK', 'KEY' => 'cubook'],
            ['ROOM' => 'soprBASIC', 'KEY' => 'sopr'],
            ['ROOM' => 'chatBOX', 'KEY' => 'chat'],
            ['ROOM' => 'ledgerREPORT', 'KEY' => 'ledger'],
            ['ROOM' => 'Toy ROMs', 'KEY' => 'toys'],
            ['ROOM' => 'secretROOM note', 'KEY' => 'secret'],
        ],
    ],
    [
        'DOM' => 'danyi',
        'BUILDING' => 'danyi.com',
        'KEY' => 'index',
        'ROOMS' => [
            ['ROOM' => 'home (remember me?)', 'KEY' => 'index'],
            ['ROOM' => 'plog', 'KEY' => 'read'],
        ],
    ],
    [
        'DOM' => 'find',
        'BUILDING' => 'Search Engine!!',
        'KEY' => 'danyi',
        'ROOMS' => [
            ['ROOM' => 'find danyi.com', 'KEY' => 'danyi'],
        ],
    ],
    [
        'DOM' => 'public',
        'BUILDING' => 'public',
        'KEY' => 'hi-from-SKY',
        'ROOMS' => [
            ['ROOM' => 'hi from SKY', 'KEY' => 'hi-from-SKY'],
        ],
    ],
    [
        'DOM' => 'EXE-708',
        'BUILDING' => "Flamiy's Corner",
        'KEY' => 'myfirstpage',
        'ROOMS' => [
            ['ROOM' => 'My First Page!!', 'KEY' => 'myfirstpage'],
        ],
    ],
    [
        'DOM' => 'games',
        'BUILDING' => 'games',
        'KEY' => 'TheKhaosDetective',
        'ROOMS' => [
            ['ROOM' => 'The Khaos Detective', 'KEY' => 'TheKhaosDetective'],
            ['ROOM' => 'KDE Notes And Chords', 'KEY' => 'KDENotesAndChords'],
        ],
    ],
    [
        'DOM' => 'roms',
        'BUILDING' => 'roms / toys',
        'KEY' => 'games',
        'ROOMS' => [
            ['ROOM' => 'Moira / toy ROMs', 'KEY' => 'games'],
        ],
    ],
    [
        'DOM' => 'private',
        'BUILDING' => 'private tests',
        'KEY' => 'testauth',
        'ROOMS' => [
            ['ROOM' => 'testauth (secretROOM)', 'KEY' => 'testauth'],
            ['ROOM' => 'betsoft-todo', 'KEY' => 'betsoft-todo'],
        ],
    ],
];
