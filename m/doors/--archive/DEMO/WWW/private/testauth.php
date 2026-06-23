<?php

SKY__AUTH(
    /*MOD_SLUG*/     "DRL-SDK",
    /*MOD_DISPLAY*/  "dani-leve", 
    
    /*DOM_SLUG*/     "find", 
    /*DOM_DISPLAY*/  "WWW Search Engine!!",

    /*ROOM_SLUG*/    "danyi.com", 
    /*ROOM_DISPLAY*/  "find danyi.com",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("find danyi.com");
getTool("secretROOM","Protect.DEMO");
leaf("This is a simple test of session state changes. This is not true authorization, but the visual concept layer of it.");
closeSky();
?>
