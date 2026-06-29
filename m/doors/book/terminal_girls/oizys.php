<?php

SKY__AUTH(
    /*MOD_SLUG*/     "km",
    /*MOD_DISPLAY*/  "Kitten Moire", 
    
    /*DOM_SLUG*/     "terminal_girls", 
    /*DOM_DISPLAY*/  "Book of Terminal Girls",

    /*ROOM_SLUG*/    "oizys", 
    /*ROOM_DISPLAY*/  "Book of Oizys Misora",

    /*ROOM_FLAVOR*/  "classic"
);

openSky(MOD_DISPLAY);
bigHeading(ROOM_DISPLAY);
hr();   
getTool("postBASIC","SoperView");    

hr();
getTool("postBASIC","MakePost"); 