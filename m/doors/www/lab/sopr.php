<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'sopr', 'soprBASIC test',
    'skyline-standard'
);

openSky('Lab · soprBASIC');
lab_open('fragments', 'sopr');

leaf('Drop a scrap under a section heading. The pile below is everything this room has collected.');
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
