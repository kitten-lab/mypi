<?php
/**
 * WWW Tool Lab — home of the pretend test site.
 */
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'home', 'Lab home',
    'classic'
);

openSky('Tool Lab');
lab_open('Welcome to the Tool Lab', 'home');

leaf('This is a **fake little website** living inside the WWW explorer chrome. Each page installs tools with `getTool` the same way real rooms do.');
leaf('Use the side nav (built with `section` / `close_section`) to hit every root tool under test.');
hr();
leaf('**cuBOOK** — guestbook form + view');
leaf('**soprBASIC** — add fragment + list');
leaf('**chatBOX** — chat form + room log');
leaf('**ledgerREPORT** — crates / charlie / TPS faces');
leaf('**Toy ROMs** — placeToy / RomHost stage');
leaf('**secretROOM** — visual auth concept (if present)');

lab_close();
closeSky();
