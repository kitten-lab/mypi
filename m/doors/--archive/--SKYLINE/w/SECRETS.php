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

getTool("reportBASIC", "ViewReport");
