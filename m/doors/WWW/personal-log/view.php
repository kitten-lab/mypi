<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "www", 
    /*MOD_DISPLAY*/  "www", 
    
    /*DOM_SLUG*/     "personal-log", 
    /*DOM_DISPLAY*/  "personal-log",

    /*ROOM_SLUG*/    "write", 
    /*ROOM_DISPLAY*/  "www's plog",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky('plog');

getTool("postBASIC","ViewList");    

closeSky();

 ?>