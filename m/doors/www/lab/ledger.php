<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'ledger', 'ledgerREPORT test',
    'classic'
);

openSky('Lab · ledgerREPORT');
lab_open('ledgerREPORT · faces', 'ledger');

leaf('Same tool faces Starline uses — crates / charlie / TPS if the pages exist.');
hr();

section('', 'lab-tool-view');
medHeading('Crates');
getTool('ledgerREPORT', 'Crates');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Charlie');
getTool('ledgerREPORT', 'Charlie');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('TPS');
getTool('ledgerREPORT', 'Tps');
close_section();

lab_close();
closeSky();
