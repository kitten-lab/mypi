<?php
SKY__AUTH(
    /*MOD_SLUG*/     "NEWS-REPORTER",
    /*MOD_DISPLAY*/  "WRIGHT MOAR", 
    
    /*DOM_SLUG*/     "reportDepartment", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "teeHeeSecrets", 
    /*ROOM_DISPLAY*/  "SKYLINE NEWS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("SKYLINE NEWS");

title("Skyline News Desk", "header", 1);
medHeading("RECENT NEWS FROM THE SILO");
getTool("reportBASIC","ViewReport");

closeSky();
?>