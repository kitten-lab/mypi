<?php
SKY__AUTH(
    /*MOD_SLUG*/     "NEWS-REPORTER",
    /*MOD_DISPLAY*/  "WRIGHT MOAR", 
    
    /*DOM_SLUG*/     "publicOffices", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "news", 
    /*ROOM_DISPLAY*/  "SKYLINE NEWS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("SKYLINE NEWS");
getDecor("I", "LOGO_OM.png","logo");
title("SKYLINE NEWS! Reporting on the SILO SYSTEM", "header", 1);
leaf("Reporting on INTERA from INSIDE!");
medHeading("RECENT NEWS FROM THE SILO");
getTool("postBASIC","StorePost.DEMO");

closeSky();
?>