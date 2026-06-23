<?php 
openSky("SKYLINE SYSTEM REPORTS");
SKY__AUTH(
    /*MOD_SLUG*/     "SKYLINE-REPORTERB",
    /*MOD_DISPLAY*/  "SKYLINE NEWS", 
    
    /*DOM_SLUG*/     "PUBLIC", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "NEWS", 
    /*MOD_DISPLAY*/  "THE NEWS ROOM",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading("Report a SKYLINE UPDATE");
getTool("postBASIC","MakePost");
closeSky();
