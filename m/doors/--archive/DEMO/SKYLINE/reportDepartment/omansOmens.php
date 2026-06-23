<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "RECORDER",
    /*MOD_DISPLAY*/  "OMAN O'MENTE", 
    
    /*DOM_SLUG*/     "reportDepartment", 
    /*DOM_DISPLAY*/  "REPORTING DEPARTMENT",

    /*ROOM_SLUG*/    "omansOmens", 
    /*MOD_DISPLAY*/  "OMAN'S OMENS",

    /*ROOM_FLAVOR*/  "omansOmens"
);


openSky(strtoupper("[ " . getMyID("sys_display") . " ] " . getMyID("dom_display") . ": " . getMyID("room_display")));

bigHeading("OM. Please record an omen.");
leaf("Where does your attention go? Any time your attention is captured at a higher intensity, this is known as an OMEN. An OMEN may be a sign, a symbol, an event, a recurring pattern. The only requirement for a qualifying OMEN is whether or not your attention was pulled to it hard enough that you'd consider recording it here.");
hr();


getTool("reportBASIC", "ListReports");
hr();

getTool("reportBASIC", "IntakeReport.DEMO");

leaf("--
Oman O'mente is using reportBASIC's IntakeReport with a special Room_Flavor applied. Applying a custom 'omansOmens' SIG FIG file modifies his form and parts out his outputs in any way he would like. The reports are printed back via reportBASIC's ListReports.");
closeSky();