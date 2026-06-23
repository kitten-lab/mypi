<?php 
openSky("REPORT A HYMN");
SKY__AUTH(
    /*MOD_SLUG*/     "PRINCESS",
    /*MOD_DISPLAY*/  "JASMINE", 
    
    /*DOM_SLUG*/     "reportDepartment", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "songOfSongs", 
    /*MOD_DISPLAY*/  "SONG OF SONGS",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

bigHeading("The ineffable song, do you hear HYMN?");

leaf("Where does your attention go? Any time your attention is captured at a higher intensity, this is known as an OMEN. An OMEN may be a sign, a symbol, an event, a recurring pattern. The only requirement for a qualifying OMEN is whether or not your attention was pulled to it hard enough that you'd consider recording it here.");
hr();


getTool("reportBASIC", "ListReports");
hr();

getTool("reportBASIC", "IntakeReport.DEMO");
closeSky();