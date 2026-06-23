<?php
SKY__AUTH(
    /*MOD_SLUG*/     "DEMO-SDK",
    /*MOD_DISPLAY*/  "sdk-808", 
    
    /*DOM_SLUG*/     "run", 
    /*DOM_DISPLAY*/  "terminal.root",

    /*ROOM_SLUG*/    "prolog", 
    /*ROOM_DISPLAY*/  "limited access",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("limited access");


getTool("chestersTOYBOX","TerminalProlog");

closeSky();
?>