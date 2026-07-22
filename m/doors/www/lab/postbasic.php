<?php
/**
 * WWW Tool Lab · postBASIC (ledger kind=post) — same rail as Starline News.
 */
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'postbasic', 'postBASIC test',
    'classic'
);

openSky('Lab · postBASIC');
lab_open('postBASIC · headlines / posts', 'postbasic');

leaf('Writes **ledger** crates `kind=post` via `mypi_ledger_create_post` (Charlie tags + TPS). Same store as Skyline News.');
leaf('Form: topic + body + optional Charlie threads (`this*rel&gt;that`). List below is SoperView for this lab place.');
hr();

section('', 'lab-tool-form');
medHeading('Make a post');
getTool('postBASIC', 'MakePost');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Posts in this lab (ledger)');
getTool('postBASIC', 'SoperView');
close_section();

lab_close();
closeSky();
