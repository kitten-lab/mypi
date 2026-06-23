<?php
openSky("VIEWING PUBLIC_USER REPORTS");
SKY__AUTH(
    /*MOD_SLUG*/     "SKYLINE-REPORTER",
    /*MOD_DISPLAY*/  "SKYLINE NEWS", 
    
    /*DOM_SLUG*/     "PUBLIC", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "NEWS", 
    /*MOD_DISPLAY*/  "THE NEWS ROOM",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading($GLOBALS[$SITE]['ROOM_SLUG']);

getTool("postBASIC","ViewList");

leaf("<br><br><br><a href='?PUBLIC=NEWS-REPORT'>Post</a>");
closeSky();
?>