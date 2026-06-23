<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DW",
    /*MOD_DISPLAY*/  "DANIEL WAKE", 
    
    /*DOM_SLUG*/     "symbolCRAFT", 
    /*DOM_DISPLAY*/  "symbolCRAFT slots",

    /*ROOM_SLUG*/    "[slots]=detective-case_work", 
    /*ROOM_DISPLAY*/  "slots > detective > case work",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);
section('', "section_container");
    section('', "fragments");
        medHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        bigHeading("Game Documentation - Rough Draft");
        getTool("soprBASIC", "ViewList");
    close_section();
#    section('','inputs');
#        medHeading("jsonREADER");
#        section('background-color:black;color:white; width: 400px; height: 250px; overflow:scroll; padding: 12px; margin: 8px 0', 'json-reader');
#          getTool("mdREADER", "READER");
#        close_section();
#        medHeading("soprBASIC");
#        getTool("soprBASIC", "AddFragment");
#    close_section();
close_section();
closeSky();