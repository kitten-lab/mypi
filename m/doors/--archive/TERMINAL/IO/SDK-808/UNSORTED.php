<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SDK-808",
    /*MOD_DISPLAY*/  "SDK-808", 
    
    /*DOM_SLUG*/     "IO", 
    /*DOM_DISPLAY*/  "IO",

    /*ROOM_SLUG*/    "FILES_UNSORTED_SDK-808", 
    /*ROOM_DISPLAY*/  "SAM'S FILES > UNSORTED",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);


bigHeading("TERMINAL.IO FILE EXPLORER");
leaf($GLOBALS[$SITE]['ROOM_SLUG'] . "<br>");
medHeading("SAM'S FILES > UNSORTED");
getTool("postBASIC", "ViewList");
getTool("postBASIC", "charliePOST");

closeSky();