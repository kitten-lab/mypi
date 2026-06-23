<?php 
openSky("FILE A REPORT");
SKY__AUTH(
    "SIGHTSMAN", // storage slug of #MOD
    "SIR SIGHTSMAN", // display name of MOD
    "REGISTRAR", // building slug #DOM
    "REGISTRATIONS DEPARTMENT", // building display name
    "REGISTER-WORLD", // room slug #ROOM
    "THE WORLD SIGHT CREATION ROOM",// room display name
    "skyline-standard"
);

bigHeading("Register a world with the SKYLINE!");
medHeading("Enter the details and spawn a whole new world.");

getTool("skyGenesis","CreateWorld");


closeSky();

 ?>