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

leaf('System-wide `placeToy` / RomHost — two shells that should not eat each other.');
hr();

romStage();
section('', 'lab-toy-shelf');
shelf('labToyShelf');
placeToy('MRA-001', 'Julie');
placeToy('KCD-001', 'ClassicBoi');
close_shelf();
close_section();

hr();
showToyCatalog();

lab_close();
closeSky();
