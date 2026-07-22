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

leaf('A small pretend site tucked inside the WWW window. Side rooms try tools the way real doors do.');
leaf('Pick a room from the rail — posts, files, guest book, fragments, chat, reports, toys.');
hr();
leaf('**Posts** — headlines and body text');
leaf('**Files** — markdown desk, view and revise');
leaf('**Guest book** — sign and read');
leaf('**Fragments** — short scraps in a pile');
leaf('**Chat** — hangout room');
leaf('**Reports** — crates, Charlie, TPS faces');
leaf('**Toys** — little windows on the stage');
leaf('**Secret** — pretend lock (when installed)');

lab_close();
closeSky();
