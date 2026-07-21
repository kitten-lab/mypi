<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'sopr', 'soprBASIC test',
    'skyline-standard'
);

openSky('Lab · soprBASIC');
lab_open('soprBASIC · fragments', 'sopr');

leaf('Fragments go to ledger `kind=soper` (section = topic). Old JSON chests: `t/tools/-v3/soprBASIC-json`.');
hr();

section('', 'lab-tool-form');
medHeading('Add fragment');
getTool('soprBASIC', 'AddFragment');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Fragment list');
getTool('soprBASIC', 'ViewList');
close_section();

lab_close();
closeSky();
