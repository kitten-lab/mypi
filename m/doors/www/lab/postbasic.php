<?php
/**
 * WWW Tool Lab · postBASIC — same rail as Starline News.
 */
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'postbasic', 'postBASIC test',
    'classic'
);

openSky('Lab · postBASIC');
lab_open('posts & headlines', 'postbasic');

leaf('Write a headline, leave a body, optionally thread a few names. What you post here lands in the same pile as Skyline News.');
leaf('List below is everything this lab room has already said.');
hr();

section('', 'lab-tool-form');
medHeading('Make a post');
getTool('postBASIC', 'MakePost');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Posts in this room');
getTool('postBASIC', 'SoperView');
close_section();

lab_close();
closeSky();
