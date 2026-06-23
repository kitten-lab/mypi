<?php
SKY__AUTH(
    /*MOD_SLUG*/     "NEWS-REPORTER",
    /*MOD_DISPLAY*/  "WRIGHT MOAR", 
    
    /*DOM_SLUG*/     "offices", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "news", 
    /*ROOM_DISPLAY*/  "SKYLINE NEWS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("SKYLINE NEWS");


title("Skyline News Desk", "header", 1);
medHeading("RECENT NEWS FROM THE SILO");
getTool("postBASIC","ViewPost");

closeSky();
?>