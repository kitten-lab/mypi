<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'toys', 'Toy ROM test',
    'bar-b_games'
);

openSky('Lab · toys');
lab_open('Toy ROMs · stage', 'toys');

leaf('Little machines on the stage. Open two if you like — they should not swallow each other.');
hr();

// Quiet chat install so CBX-001 can post + boot lines on this page too
$GLOBALS['CHATBOX_QUIET_PAGES'] = true;
getTool('chatBOX', 'ChatBox');
getTool('chatBOX', 'ChatRoom');

romStage();
section('', 'lab-toy-shelf');
shelf('labToyShelf');
placeToy('MRA-001', 'Julie');
placeToy('KCD-001', 'ClassicBoi');
placeToy('CBX-001', 'ClassicBoi');
close_shelf();
close_section();

hr();
showToyCatalog();

lab_close();
closeSky();
