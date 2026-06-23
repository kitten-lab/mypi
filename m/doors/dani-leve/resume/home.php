<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DRL", 
    /*MOD_DISPLAY*/  "Danielle Leve (Rudolph)", 
    
    /*DOM_SLUG*/     "resume", 
    /*DOM_DISPLAY*/  "resume",

    /*ROOM_SLUG*/    "home", 
    /*ROOM_DISPLAY*/  "resume home",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

openSky('Portfolio Resume for Dani Leve');

getTool("postBASIC", "SoperView");
skylite("<img src='". i_root ."/dani-leve/portfolio/adm_editor.png' style='width:333px'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/adm_openmenu.png' style='width:333px'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/adm_multiapp.png' style='width:333px'>");
    leaf("ADM & THE DREAM MACHINE");
    skylite("<img src='". i_root ."/dani-leve/portfolio/adm_selectingchars.png' style='width:333px'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/adm_intavern.png' style='width:333px'> ");
    leaf('TILES: A STUDY IN SELF SUSTAINING NARRATIVE LOOPS');
    skylite("<img src='". i_root ."/dani-leve/portfolio/tiles_screen1.png' style='width:333px'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/tiles_screen2.png' style='width:333px'> ");
    leaf('SOMETHING MATTERED HERE: A GAME ABOUT REMEMBERING');
    skylite("<img src='". i_root ."/dani-leve/portfolio/smh_style1.png' style='width:333px'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/smh_style2.png' style='width:333px'> ");
    leaf('SILO: MY POCKET INTERNET');
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_mypi.png' style='width:333px'> ");

closeSky();

 ?>