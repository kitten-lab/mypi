<?php
SKY__AUTH(
    /*MOD_SLUG*/     "{{MOD_SLUG}}",
    /*MOD_DISPLAY*/  "{{MOD_DISPLAY}}", 
    
    /*DOM_SLUG*/     "{{DOM_SLUG}}", 
    /*DOM_DISPLAY*/  "{{DOM_DISPLAY}}",

    /*ROOM_SLUG*/    "{{KEY_SLUG}}", 
    /*ROOM_DISPLAY*/  "{{KEY_DISPLAY}}",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("{{KEY_DISPLAY}}");


bigHeading("Welcome to home {{DOM_DISPLAY}}! You are now on SKYLINE On INTERA.");
getTool("keyMAKER2","MakeKey");

closeSky();
?>