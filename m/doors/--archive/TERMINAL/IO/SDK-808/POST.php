<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SDK-808",
    /*MOD_DISPLAY*/  "SDK-808", 
    
    /*DOM_SLUG*/     "IO", 
    /*DOM_DISPLAY*/  "IO",

    /*ROOM_SLUG*/    "INVENTORIES_SDK-808", 
    /*ROOM_DISPLAY*/  "POST A DAILY INVENTORY",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);


bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
leaf("Thank you for your presence. May we direct you to the correct room?");
getTool("postBASIC", "charliePOST");
closeSky();