<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'ledger', 'ledgerREPORT test',
    'classic'
);

openSky('Lab · ledgerREPORT');
lab_open('reports', 'ledger');

leaf('Three faces on the same store: crates on the shelf, Charlie’s names, TPS windows. Same glass Starline uses when the rooms are installed.');
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
