<?php
openSky("SKYLINE FRONT DESK");
SKY__AUTH(
    /*MOD_SLUG*/     "SKYLINE-AGENT",
    /*MOD_DISPLAY*/  "SKYLINE PUBLIC OFFICIAL", 
    
    /*DOM_SLUG*/     "PUBLIC", 
    /*DOM_DISPLAY*/  "PUBLIC OFFICE",

    /*ROOM_SLUG*/    "FRONT-DESK", 
    /*MOD_DISPLAY*/  "WELCOME DESK",

    /*ROOM_FLAVOR*/  "skyline-standard"
);


bigHeading("Welcome to SKYLINE On INTERA.");
medHeading("THANK YOU FOR CHECKING IN.");

leaf("Thank you for entering our SIGHT. We CUKRA.");

leaf("Consider submitting a vision report. We see what you see. Let us know.");
summonTool('reportBASIC', 'IntakeReport');
medHeading("Check-ins");
summonTool('reportBASIC', 'CheckInList');


closeSky();
?>