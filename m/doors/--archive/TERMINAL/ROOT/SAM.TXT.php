<?php 
openSky("TERMINAL/ROOT");
SKY__AUTH(
    /*MOD_SLUG*/     "TERMINAL",
    /*MOD_DISPLAY*/  "TERMINAL", 
    
    /*DOM_SLUG*/     "ROOT", 
    /*DOM_DISPLAY*/  "ROOT",

    /*ROOM_SLUG*/    "ROOT", 
    /*ROOM_DISPLAY*/  "ROOT",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading($GLOBALS['SITE']);
getTool("postBASIC", "SoperView");
closeSky();