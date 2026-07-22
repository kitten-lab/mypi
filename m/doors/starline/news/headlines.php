<?php
/**
 * Starline · DOM news · ROOM headlines
 * System-coming-online board — postBASIC → ledger.
 *
 * Cosmology: SYS starline / DOM news / ROOM headlines / MOD (below)
 * Compat defines still WORLD_* / BLOCK_* on SIG.
 *
 * All visible HTML must go through skylite() → GETS['set'] (never raw echo).
 */

SKY__AUTH(
/* mod */  'system', 'System Voice',
/* dom */  'news', 'News',
/* room */ 'headlines', 'Headlines — System Online',
/* texture */ 'classic'
);

openSky(ROOM_DISPLAY);
h1(ROOM_DISPLAY);

skylite(
    '<p class="lede">'
    . 'Starline News. Headlines land in the <strong>mypi ledger</strong> '
    . '(<code>d/_LEDGER/mypi.sqlite</code>) with SYS/DOM/ROOM/MOD and event history. '
    . 'Not room paper slips.'
    . '</p>'
);

getTool('postBASIC', 'MakePost');
hr();
getTool('postBASIC', 'SoperView');

closeSky();
