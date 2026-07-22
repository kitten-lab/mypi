<?php
/**
 * WWW Tool Lab · fileKeeper — same tool as Terminal IO files desk.
 */
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'filekeeper', 'fileKeeper desk',
    'classic'
);

openSky('Lab · fileKeeper');
lab_open('file desk', 'filekeeper');

leaf('A quiet desk for notes. **View** a slip, **Modify** to revise — older versions stay stacked underneath.');
leaf('Same desk as the green house files room; here it sits in lab light instead.');
hr();

section('', 'lab-tool-view');
getTool('fileKeeper', 'Desk');
close_section();

lab_close();
closeSky();
