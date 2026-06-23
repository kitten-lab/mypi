<?php 
openSky("SECRET KEEPER");
SKY__AUTH(
    /*MOD_SLUG*/     "THE-CU", 
    /*MOD_DISPLAY*/  "THE CU", 
    
    /*DOM_SLUG*/     "REPORT", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "SECRETS", 
    /*MOD_DISPLAY*/  "THE SECRET ROOM",

    /*ROOM_FLAVOR*/  "tee-hee-secrets"
);

bigHeading("DO YOU HAVE A SECRET?");
medHeading("You are hidden here.");
getTool("reportBASIC","IntakeReport");
bigHeading("Secrets posted");
getTool("reportBASIC","ListReports");
closeSky();