<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'secret', 'secretROOM test',
    'classic'
);

openSky('Lab · secretROOM');
lab_open('secretROOM · visual auth', 'secret');

leaf('Concept layer only — not real security. Uses Protect.DEMO if present under -v2.');
hr();

section('', 'lab-tool-form');
// live root tools path may not have secretROOM; try v2-style name if getTool finds nothing it just warns
if (is_file(echoSONAR . 't/tools/-v2/secretROOM/pageLogin.php')) {
    // not on getTool path (needs t/tools/secretROOM) — show note
    leaf('secretROOM still lives under `t/tools/-v2/secretROOM`. Copy or symlink to root tools when you want full install.');
} else {
    leaf('No secretROOM package at `t/tools/secretROOM` yet.');
}
// Attempt classic name if user later promotes it
// getTool('secretROOM', 'Login');
close_section();

lab_close();
closeSky();
