<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "jack-nim", 
    /*MOD_DISPLAY*/  "Jack Nim", 
    /*DOM_SLUG*/     "fragments", 
    /*DOM_DISPLAY*/  "The E Wire Logger",
    /*ROOM_SLUG*/    "connection", 
    /*ROOM_DISPLAY*/  "Fragments of Connection",

    /*ROOM_FLAVOR*/  
);
openSky(WORLD_TAG);
bigHeading(WORLD_TAG . " - " . ROOM_DISPLAY);
getTool("soprBASIC","AddFragment"); 
hr();   
getTool("soprBASIC","ViewList");    

closeSky();

 ?>