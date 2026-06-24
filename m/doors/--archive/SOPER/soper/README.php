<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "1-MIRA-1",
    /*MOD_DISPLAY*/  "1-MIRA-1", 
    
    /*DOM_SLUG*/     "soper", 
    /*DOM_DISPLAY*/  "soper",

    /*ROOM_SLUG*/    "README", 
    /*ROOM_DISPLAY*/  "soper_README.post",

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