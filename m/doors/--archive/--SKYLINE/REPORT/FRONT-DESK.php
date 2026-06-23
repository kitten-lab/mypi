<?php 
openSky("FILE A REPORT");
SKY__AUTH(
    /*MOD_SLUG*/     "AGENT",
    /*MOD_DISPLAY*/  "REPORT AGENT", 
    
    /*DOM_SLUG*/     "REPORT", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "FRONT-DESK", 
    /*MOD_DISPLAY*/  "REPORTS CHECK-IN",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading("SKYLINE REPORT FRONT DESK");
leaf("Thank you for your presence. May we direct you to the correct room?");
closeSky();