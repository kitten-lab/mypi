<?php
require_once __DIR__ . '/_lab_site.php';

SKY__AUTH(
    'lab-mod', 'Tool Lab',
    'lab', 'Tool Lab site',
    'chat', 'chatBOX test',
    'classic'
);

openSky('Lab · chatBOX');
lab_open('chatBOX · room', 'chat');

leaf('**Live hangout:** each line is a ledger crate `kind=chat` with `meta.session` (default `live`). Open another session id to start a parallel hangout at this place.');
leaf('Room list is oldest → newest. Session switcher appears after the first lines land.');
hr();

section('', 'lab-tool-form');
medHeading('Say something');
getTool('chatBOX', 'ChatBox');
close_section();

hr();

section('', 'lab-tool-view');
medHeading('Chat log');
getTool('chatBOX', 'ChatRoom');
close_section();

lab_close();
closeSky();
