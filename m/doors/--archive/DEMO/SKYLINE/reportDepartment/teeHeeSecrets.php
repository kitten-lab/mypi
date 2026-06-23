<?php 
openSky("REPORT A HYMN");
SKY__AUTH(
    /*MOD_SLUG*/     "SECRET-KEEPER",
    /*MOD_DISPLAY*/  "THE-CU", 
    
    /*DOM_SLUG*/     "reportDepartment", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "teeHeeSecrets", 
    /*MOD_DISPLAY*/  "THE SECRET ROOM",

    /*ROOM_FLAVOR*/  "tee-hee-secrets"
);

bigHeading("Shh.. secret--! Ah, you again!");
leaf("Where does your attention go? Any time your attention is captured at a higher intensity, this is known as an OMEN. An OMEN may be a sign, a symbol, an event, a recurring pattern. The only requirement for a qualifying OMEN is whether or not your attention was pulled to it hard enough that you'd consider recording it here.");
hr();
getTool("reportBASIC", "IntakeReport.DEMO");

hr();

getTool("reportBASIC", "CheckInList");
closeSky();