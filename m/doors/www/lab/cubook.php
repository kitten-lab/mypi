<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'cubook', 'cuBOOK test',
    'early-web'
);

openSky('Lab · cuBOOK');
lab_open('cuBOOK · guestbook', 'cubook');

leaf('Writes **ledger** crates `kind=guestcu` (SQLite). JSON guestbook is archived under `t/tools/-v3/cuBOOK-json`.');
leaf('Base layout is neutral tool CSS; override tokens e.g. `--cuBOOK-btn-bg` on `.formContainer`.');
// Example surface override (not a hard theme — shows the --token path)
quickDressing('lab-tool-form .formContainer, .lab-tool-form form', '
  --cuBOOK-input-border: 1px solid #5a8a5a;
  --cuBOOK-btn-border: 1px solid #3a6a3a;
  --cuBOOK-btn-bg: #e8f4e8;
');
hr();

section('', 'lab-tool-form');
medHeading('Sign the book');
getTool('cuBOOK', 'GuestPOST.DEMO');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Entries');
getTool('cuBOOK', 'ViewCUs');
close_section();

lab_close();
closeSky();
