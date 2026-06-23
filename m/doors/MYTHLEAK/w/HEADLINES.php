<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "MCS-000",
    /*MOD_DISPLAY*/  "-MOUSE-", 
    
    /*DOM_SLUG*/     "NEWS", 
    /*DOM_DISPLAY*/  "THE JUICE LINE",

    /*ROOM_SLUG*/    "HEADLINES", 
    /*ROOM_DISPLAY*/  "HEADLINES",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

section('', "section_container");
    section('', "headlines");

        bigHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("postBASIC", "Headerlines");
    close_section();
    section('','content');
        getTool("postBASIC", "ViewPost");

    close_section();
    section('', "ads");
    close_section();
close_section();
closeSky();