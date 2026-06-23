<?php 
openSky("RECORD AN OMEN");
SKY__AUTH(
    /*MOD_SLUG*/     "OM", 
    /*MOD_DISPLAY*/  "OMAN", 
    
    /*DOM_SLUG*/     "REPORT", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "OMENS", 
    /*MOD_DISPLAY*/  "OMAN'S OMENS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
bigHeading("OM. YOUR OMEN.");

getTool("reportBASIC", "ViewReport");

getTool("reportBASIC", "ListReports");
closeSky();