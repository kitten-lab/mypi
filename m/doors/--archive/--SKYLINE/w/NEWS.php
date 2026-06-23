<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SKYLINE-REPORTER",
    /*MOD_DISPLAY*/  "SKYLINE NEWS", 
    
    /*DOM_SLUG*/     "PUBLIC", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "NEWS", 
    /*MOD_DISPLAY*/  "THE NEWS ROOM",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("VIEWING PUBLIC_USER REPORTS");
getTool("postBASIC","ViewPost");
closeSky();
?>