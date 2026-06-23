<?php 
openSky("REPORT A HYMN");
SKY__AUTH(
    /*MOD_SLUG*/     "SKYLINE-AGENT",
    /*MOD_DISPLAY*/  "SKYLINE PUBLIC OFFICIAL", 
    
    /*DOM_SLUG*/     "PUBLIC", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICE",

    /*ROOM_SLUG*/    "FRONT-DESK", 
    /*MOD_DISPLAY*/  "WELCOME DESK",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading("HAVE YOU FELT HYMN?");

getTool("reportBASIC", "ViewReport");