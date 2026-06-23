<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DW",
    /*MOD_DISPLAY*/  "DANIEL WAKE", 
    
    /*DOM_SLUG*/     "symbolCRAFT", 
    /*DOM_DISPLAY*/  "symbolCRAFT slots",

    /*ROOM_SLUG*/    "business-plan", 
    /*ROOM_DISPLAY*/  "business-plan",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);
section('', "section_container");
    section('', "fragments");
        medHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("soprBASIC", "ViewList");
    close_section();
    section('','inputs');
        medHeading("jsonREADER");
        section('background-color:black;color:white; width: 400px; height: 250px; overflow:scroll; padding: 12px; margin: 8px 0', 'json-reader');
          getTool("json", "Reader");
        close_section();
        medHeading("soprBASIC");
        getTool("soprBASIC", "AddFragment");
    close_section();
close_section();
closeSky();