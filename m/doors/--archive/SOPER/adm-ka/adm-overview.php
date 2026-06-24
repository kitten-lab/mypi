<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "1-MIRA-1",
    /*MOD_DISPLAY*/  "1-MIRA-1", 
    
    /*DOM_SLUG*/     "adm-ka", 
    /*DOM_DISPLAY*/  "adm-ka",

    /*ROOM_SLUG*/    "adm-overview", 
    /*ROOM_DISPLAY*/  "adm-overview",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

section('', "section_container");
    section('', "fragments");
        bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        #getTool("postBASIC", "SoperView");
    close_section();
    section('','inputs');
        leaf("Thank you for your presence.");
        getTool("soprBASIC", "AddFragment");
    close_section();
close_section();
closeSky();