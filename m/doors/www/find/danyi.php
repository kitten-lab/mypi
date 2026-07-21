<?php
SKY__AUTH(
    /*MOD_SLUG*/     "DRL-SDK",
    /*MOD_DISPLAY*/  "dani-leve", 
    
    /*DOM_SLUG*/     "find", 
    /*DOM_DISPLAY*/  "WWW Search Engine!!",

    /*ROOM_SLUG*/    "danyi.com", 
    /*ROOM_DISPLAY*/  "find danyi.com",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("find danyi.com");
medHeading("Links");
// b-front path; leadline hides /www when you are already inside
leaf("<a href=\"/www/danyi/index\">Here</a> — remember me? its danyi.com");
closeSky();
?>