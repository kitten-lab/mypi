<?php 
openSky("REPORT A HYMN");
SKY__AUTH(
    /*MOD_SLUG*/     "JASMINE",
    /*MOD_DISPLAY*/  "PRINCESS JASMINE", 
    
    /*DOM_SLUG*/     "REPORT", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "HYMN", 
    /*MOD_DISPLAY*/  "SONG OF SONGS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading("HAVE YOU FELT HYMN?");

getTool("reportBASIC", "ViewReport");