<?php
/**
 * WWW Tool Lab · chatBOX + CBX-001 Chatterbox ROM.
 */
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'chat', 'chatBOX test',
    'classic'
);

// Tool pages stay silent; ROM owns the hangout chrome
$GLOBALS['CHATBOX_QUIET_PAGES'] = true;

openSky('Lab · chatBOX');
lab_open('chatterbox', 'chat');

leaf('**CBX-001** — click the cover. Pick a nick, a hangout name if you want a private corner, then say something.');
leaf('The form log stays quiet here; the little window is the room.');
hr();

// Actors + boot JSON (no classic form/log chrome)
getTool('chatBOX', 'ChatBox');
getTool('chatBOX', 'ChatRoom');

hr();
romStage();
section('', 'lab-toy-shelf');
shelf('cbxShelf');
placeToy('CBX-001', 'ClassicBoi');
close_shelf();
close_section();

lab_close();
closeSky();
