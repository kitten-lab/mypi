<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'secret', 'secretROOM test',
    'classic'
);

openSky('Lab · secretROOM');
lab_open('secret door', 'secret');

leaf('A pretend lock for the concept layer — not a real vault.');
hr();

section('', 'lab-tool-form');
if (is_file(echoSONAR . 't/tools/-v2/secretROOM/pageLogin.php')) {
    leaf('The secret room still sleeps in the archive. When it wakes, it will hang here.');
} else {
    leaf('No secret door installed yet. The frame is waiting.');
}
close_section();

lab_close();
closeSky();
