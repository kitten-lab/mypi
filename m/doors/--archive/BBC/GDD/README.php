<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DW",
    /*MOD_DISPLAY*/  "DANIEL WAKE", 
    
    /*DOM_SLUG*/     "GDD", 
    /*DOM_DISPLAY*/  "GAME DESIGN DISTRICT",

    /*ROOM_SLUG*/    "README", 
    /*ROOM_DISPLAY*/  "WELCOME TO THE CHAOS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

section('', "section_container");
    section('', "fragments");
        bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("postBASIC", "ViewPost");
    close_section();
    section('','inputs');
        leaf("Please consider posting something here.");
        getTool("postBASIC", "MakePost");
    close_section();
close_section();
closeSky();