<?php
SKY__AUTH(
    /*MOD_SLUG*/     "WELCOME-AGENT",
    /*MOD_DISPLAY*/  "RHEA PORTA", 
    
    /*DOM_SLUG*/     "reportDepartment", 
    /*DOM_DISPLAY*/  "REPORTS DEPARTMENT",

    /*ROOM_SLUG*/    "frontDesk", 
    /*MOD_DISPLAY*/  "RECEPTION DESK",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

openSky($GLOBALS[$SITE]['SYS_DISPLAY'] . " " . $GLOBALS[$SITE]['ROOM_DISPLAY']);
bigHeading("Report Department: Reception Desk");
getTool("postBASIC","SoperView");

leaf("--
Content on this page was ingested with postBASIC and is being displayed in SoperView");

closeSky();
?>