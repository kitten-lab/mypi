<?php
SKY__AUTH(
    /*MOD_SLUG*/     "WELCOME-AGENT",
    /*MOD_DISPLAY*/  "SKYLINE PUBLIC OFFICIAL", 
    
    /*DOM_SLUG*/     "public", 
    /*DOM_DISPLAY*/  "public",

    /*ROOM_SLUG*/    "hi-from-SKY", 
    /*MOD_DISPLAY*/  "SKYLINE-WELCOME",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

openSky("SKYLINE WELCOME MAT");

bigHeading("Welcome to the DEMO of MY POCKET INTERNET!");
medHeading("a personal thought habitat project.");

leaf("We have a few surfaces online, feel free to explore!");
leaf("<a href='" . b_root . "/DEMO/WWW/'>WWW</a>");
leaf("<a href='" . b_root . "/DEMO/SKYLINE/'>SKYLINE</a>");

closeSky();
?>