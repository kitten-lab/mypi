<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "SDK-808",
    /*MOD_DISPLAY*/  "SDK-808", 
    
    /*DOM_SLUG*/     "IO", 
    /*DOM_DISPLAY*/  "IO",

    /*ROOM_SLUG*/    "FILES_MUSIC_SDK-808", 
    /*ROOM_DISPLAY*/  "SHARE YOUR MUSIC",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky($GLOBALS[$SITE]['ROOM_DISPLAY']);


bigHeading("TERMINAL.IO FILE EXPLORER");
leaf($GLOBALS[$SITE]['ROOM_SLUG'] . "<br>");
medHeading("SAM'S FILES > MUSIC");
getTool("JUKEBOX", "MySongCatalog");
medHeading("Post a New File Here");
getTool("JUKEBOX", "UploadSong");
hr();

closeSky();