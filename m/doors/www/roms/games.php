<?php
/**
 * Toy ROM play shelf — system placeToy / romStage (not WWW-only).
 * Two shells at once so they no longer eat each other.
 */
global $MAP;

SKY__AUTH(
    'DOLLE-bV', 'Barb E. Vale',
    'roms', 'Toy ROMs',
    'games', 'Moira + shell test',
    'bar-b_games'
);

quickDressing('wwwExplorer_innerShell', '
  background-color: darkblue;
');

openSky('APPS > Toy ROMs');

title('My Toy ROMs!', 'toyROM_header', 1);
leaf('Click a cover to open a **window on this stage**. Two shells can be open at once.');

// system-wide install (same spirit as getTool)
romStage();
shelf('toyShelf');
placeToy('MRA-001', 'Julie');
placeToy('KCD-001', 'ClassicBoi');
close_shelf();

hr();
showToyCatalog();

closeSky();
