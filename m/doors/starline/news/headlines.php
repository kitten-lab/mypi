<?php
/**
 * Skyline · DOM news · ROOM headlines
 * System-coming-online board — postBASIC → ledger.
 *
 * Cosmology: SYS starline / DOM news / ROOM headlines / MOD (below)
 * Compat defines still WORLD_* / BLOCK_* on SIG.
 */

SKY__AUTH(
/* mod */  'system', 'System Voice',
/* dom */  'news', 'News',
/* room */ 'headlines', 'Headlines — System Online',
/* texture */ 'classic'
);

openSky(ROOM_DISPLAY);

echo '<p class="lede" style="opacity:0.85;max-width:40rem;">';
echo 'Skyline News. Headlines land in the <strong>mypi ledger</strong> ';
echo '(<code>d/_LEDGER/mypi.sqlite</code>) with SYS/DOM/ROOM/MOD and event history. ';
echo 'Not room paper slips.';
echo '</p>';

getTool('postBASIC', 'MakePost');
echo '<hr style="border:0;border-top:1px solid #2a4a38;margin:1.5rem 0;">';
getTool('postBASIC', 'SoperView');

closeSky();
