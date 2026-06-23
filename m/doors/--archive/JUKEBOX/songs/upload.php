<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "JUKE", 
    /*MOD_DISPLAY*/  "JUKE", 
    
    /*DOM_SLUG*/     "songs", 
    /*DOM_DISPLAY*/  "song_server",

    /*ROOM_SLUG*/    "list", 
    /*ROOM_DISPLAY*/  "upload_song",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

openSky('SERVER OF SONGS');

bigHeading("Hi");
hr();
getTool("JUKEBOX","UploadSong");

closeSky();

 ?>