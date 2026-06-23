<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SDK-808",
    /*MOD_DISPLAY*/  "SDK-808", 
    
    /*DOM_SLUG*/     "IO", 
    /*DOM_DISPLAY*/  "IO",

    /*ROOM_SLUG*/    "FILES_ALEPH-BET_SDK-808", 
    /*ROOM_DISPLAY*/  "SHARE YOUR MUSIC",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);


bigHeading("TERMINAL.IO FILE EXPLORER");
leaf($GLOBALS[$SITE]['ROOM_SLUG'] . "<br>");
medHeading("SAM'S FILES > ALEPH-BET A-US");
getTool("postBASIC", "ViewList");

closeSky();