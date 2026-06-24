<?php 

SKY__AUTH(
    /*MOD_SLUG*/     "jack-nim", 
    /*MOD_DISPLAY*/  "Jack Nim", 
    /*DOM_SLUG*/     "elog", 
    /*DOM_DISPLAY*/  "The E Wire Logger",
    /*ROOM_SLUG*/    "index", 
    /*ROOM_DISPLAY*/  "The Root of Reason",

    /*ROOM_FLAVOR*/  
);
openSky(WORLD_TAG);

getTool("soprBASIC","AddFragment"); 
hr();   
getTool("soprBASIC","ViewList");    

closeSky();

 ?>