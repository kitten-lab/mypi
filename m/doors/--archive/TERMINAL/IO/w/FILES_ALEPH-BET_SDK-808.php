<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SDK-808",
    /*MOD_DISPLAY*/  "SDK-808", 
    
    /*DOM_SLUG*/     "IO", 
    /*DOM_DISPLAY*/  "IO",

    /*ROOM_SLUG*/    "FILES_ALEPH-BET_SDK-808", 
    /*ROOM_DISPLAY*/  "TERMINAL FILE VIEWER",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);


bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
getTool("postBASIC", "ViewPost");
