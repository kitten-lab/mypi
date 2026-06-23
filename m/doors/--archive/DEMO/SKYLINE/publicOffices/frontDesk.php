<?php

SKY__AUTH(
    /*MOD_SLUG*/     "WELCOME-AGENT",
    /*MOD_DISPLAY*/  "RHEA CEPCIAN", 
    
    /*DOM_SLUG*/     "publicOffices", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICES",

    /*ROOM_SLUG*/    "frontDesk", 
    /*MOD_DISPLAY*/  "RECEPTION DESK",

    /*ROOM_FLAVOR*/  "skyline-standard"
);
openSky("Welcome to the SKYLINE(Demo)");
title("A Sign hangs over the large desk. 'Welcome Home, Weary Traveler', it says.", "magfrag", 1);
getTool("postBASIC", "SoperView");
title("There is a paper here. 'cuBOOK Guest Check In: Only If you like', it says.", "id1' class='slug", 2);
getTool("cuBOOK", "GuestPOST.DEMO");
title("guestPOSTs in the cuBOOK", "id2' class='slug", 2);
getTool("cuBOOK", "ViewCUs");
leaf("--
Content on this page was ingested with postBASIC and is being displayed in SoperView");
closeSky();
?>