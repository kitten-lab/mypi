<?php
/**
 * Shelf · Tools — install tools into the page (and optional shelf-Tools bin later).
 * Soft-string dream:
 *   loadShelf of 'Tools'(
 *     setTool: postBasic MakePost viz:simpleView vox:classic;
 *   );
 * Today: getTool calls (PHP stand-in for the soft string).
 */
SKY__AUTH(
    'browser', 'Browser MOD',
    'shelves', 'Shelves',
    'tools', 'Tools shelf',
    'classic'
);

openSky(ROOM_DISPLAY);
medHeading('Shelf · Tools');
leaf("This room installs tools the same way News does — getTool — while the WWW shell shows a labeled **Tools** shelf region for future soft-string installs.");
hr();
// Soft-string stand-in: postBASIC list only (no form clutter unless wanted)
getTool('postBASIC', 'SoperView');
closeSky();
