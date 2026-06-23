<?php

SKY__AUTH(
    /*MOD_SLUG*/     "km",
    /*MOD_DISPLAY*/  "Kitten Moire", 
    
    /*DOM_SLUG*/     "terminal_girls", 
    /*DOM_DISPLAY*/  "Book of Terminal Girls",

    /*ROOM_SLUG*/    "oriel", 
    /*MOD_DISPLAY*/  "Ori'el Lightbearer",

    /*ROOM_FLAVOR*/  "classic"
);

openSky(MOD_DISPLAY);
bigHeading("Book of Ori'el Lightbringer");
hr();
getTool("soprBASIC", "AddFragment");
hr();
getTool("soprBASIC", "ViewList");