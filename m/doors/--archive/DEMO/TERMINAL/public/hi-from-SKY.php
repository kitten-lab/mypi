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

bigHeading("Welcome to home TERMINAL DEMO! You are now on SKYLINE On INTERA.");
medHeading("THANK YOU FOR JOINING US.");

leaf("Thank you for becoming part of our SIGHT. We CUKRA.");
leaf("Your first room and key is ready!");
leaf("<a href='" . b_root . "/DEMO/TERMINAL?root=access'>CLICK HERE!</a>");

closeSky();
?>