<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "jack-nim", 
    /*MOD_DISPLAY*/  "Jack Nim", 
    /*DOM_SLUG*/     "fragments", 
    /*DOM_DISPLAY*/  "The E Wire Logger",
    /*ROOM_SLUG*/    "machine-mems", 
    /*ROOM_DISPLAY*/  "Fragments of Machine Memories",

    /*ROOM_FLAVOR*/  
);
openSky(WORLD_TAG);
bigHeading(WORLD_TAG . " - " . ROOM_DISPLAY);
getTool("soprBASIC","AddFragment"); 
hr();   
getTool("soprBASIC","ViewList");    

closeSky();

 ?>