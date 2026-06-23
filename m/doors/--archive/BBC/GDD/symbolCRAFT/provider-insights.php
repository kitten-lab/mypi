<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DW",
    /*MOD_DISPLAY*/  "DANIEL WAKE", 
    
    /*DOM_SLUG*/     "symbolCRAFT", 
    /*DOM_DISPLAY*/  "symbolCRAFT slots",

    /*ROOM_SLUG*/    "provider-insights", 
    /*ROOM_DISPLAY*/  "provider-insights",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);

        section('height:200px; width:50%; overflow:scroll;', 'json');
          getTool("json", "Reader");
        close_section();
section('', "section_container");
    section('', "fragments");
        #section('height:200px; overflow:scroll;', 'json');
          #getTool("json", "Reader");
        #close_section();
        medHeading($GLOBALS[$SITE]['ROOM_DISPLAY']);
        getTool("soprBASIC", "ViewList");
        hr();
    close_section();
    section('','inputs');
        leaf("Please consider posting something here.");
        getTool("soprBASIC", "AddFragment");
    close_section();
close_section();
closeSky();