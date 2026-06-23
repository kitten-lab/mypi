<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DW",
    /*MOD_DISPLAY*/  "DANIEL WAKE", 
    
    /*DOM_SLUG*/     "symbolCRAFT", 
    /*DOM_DISPLAY*/  "symbolCRAFT slots",

    /*ROOM_SLUG*/    "home", 
    /*ROOM_DISPLAY*/  "symbolCRAFT home",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

section('', "section_container");
    section('', "fragments");
        medHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("postBASIC", "ViewList");
        hr();
    close_section();
    section('','inputs');
        leaf("Please consider posting something here.");
        getTool("postBASIC", "charliePOST");
    close_section();
close_section();
closeSky();