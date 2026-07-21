<?php
SKY__AUTH(
    'browser', 'Browser MOD',
    'shelves', 'Shelves',
    'softstring', 'Soft string (dream)',
    'classic'
);

openSky(ROOM_DISPLAY);
medHeading('Soft string (not compiled yet)');
leaf("Authoring voice we want on surfaces:");
leaf("loadShelf of 'Tools'(\n  setTool: postBasic MakePost viz:simpleView;\n  setTool: postBasic ShowList viz:modalView vox:classic;\n);");
leaf("That string will parse to getTool + viz/vox faces. Until then, doors use getTool in PHP — tools in t/ still echo HTML.");
closeSky();
