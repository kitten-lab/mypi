<?php 
openSky("REGISTRAR FRONT DESK");
SKY__AUTH(
    $site,
    $sys,
    $site,
    "SKYLINE-REGISTRAR", // storage slug of #MOD
    "REGISTRAR CLERK", // display name of MOD
    "REPORT", // building slug #DOM
    "REGISTRAR DEPARTMENT", // building display name
    "FRONT-DESK", // room slug #ROOM
    "THE REGISTAR'S DESK",// room display name
    "skyline-standard"
);

bigHeading("Thank you for arriving.");
closeSky();