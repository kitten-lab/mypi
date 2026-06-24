<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SOPER",
    /*MOD_DISPLAY*/  "SOPER", 
    
    /*DOM_SLUG*/     "soper", 
    /*DOM_DISPLAY*/  "soper",

    /*ROOM_SLUG*/    "reminders", 
    /*ROOM_DISPLAY*/  "soper-reminders",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

section('', "section_container");
    section('', "fragments");
        bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("postBASIC", "SoperView");
    close_section();
    section('','inputs');
        leaf("Thank you for your presence.");
        getTool("postBASIC", "MakePost");
    close_section();
close_section();
closeSky();