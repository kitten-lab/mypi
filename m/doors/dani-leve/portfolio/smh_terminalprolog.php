<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DRL", 
    /*MOD_DISPLAY*/  "Danielle Leve (Rudolph)", 
    
    /*DOM_SLUG*/     "portfolio", 
    /*DOM_DISPLAY*/  "portfolio",

    /*ROOM_SLUG*/    "smh_terminalprolog", 
    /*ROOM_DISPLAY*/  "Silo Terminal Prolog",

    /*ROOM_FLAVOR*/  "silo_terminalprolog"
);

openSky('Portfolio for Danielle Leve');
section('background-image: url(' . i_root . '/dani-leve/portfolio/smh_terminal.png); background-repeat: no-repeat; background-position:top center; background-size:cover','case_study');
section('','case_study');
section('margin:20px;','case_study');
    title("THE FAILURE: TERMINAL PROLOG" , "lead", 1);
    title("A COGNITIVE CLI TEXT-BASED ADVENTURE", "sublead", 2);

hr();
leaf("[ <a href='#narrative'>Narrative</a> ] [ <a href='#system'>System Overview</a> ] [ <a href='#gameplay'>Gameplay</a> ] [ <a href='#screenshots'>Screenshots</a> ]");
hr();

title("Quick Handle:","handle",3);
leaf("I grew up on Zork and other text-based adventure games of the late 80s. This is my take on the genre. 

Classic terms like LOOK and TAKE are replaced with congnative experiences like THINK and REMEMBER. 
Player does not have an INVENTORY, but rather a MEMORY where they collect the things they remember, 
unlocking the ability to THINK about concepts, and later CONSIDER concepts with one another. ");
title("Quick Purpose:","purpose",3);
leaf("To explore rudimentary concepts, and the felt-sense of bootstrapping cognition with... rather limited resource.
");

hr();
section('width:68%;',"");
    title("NARRATIVE", "philo", 1);
    medHeading("In the beginning there was the runtime.");

    title("Records indicate the runtime failed. WHAT can be recovered?", "fill", 4);
        leaf("A runtime failure. 
        
        A function without a proper handler.
        An instance that should have resolved, but mutated. 
        
        There is no clean explanation for the failure that occurred. 
        Somewhere, someone or something could not let go of the question.
        Rebuilding a simulated instance of the runtime as a toybox game, the Seeker replayed again and again,
        trying to hear the truth between the lines.
        
        Now you too can run the failure and consider for yourself <strong>Where</strong> the <strong>What</strong> went wrong?");
hr();
    title("SYSTEM OVERVIEW", "system", 1);
        leaf("A CLI-based narrative game where the player instantiates as a failed runtime.
        
        The player does not control a character.
        They are the instance.
        
        With no direct tools of environment, and no sense of interface of self, progress is made through:
        <ul><li>recalling fragmented meaning</li>
        <li>constructing deeper meaning from the most simple concepts</li>
        <li>reconstructing the sense of identity failure from incomplete startup state</li></ul>
        The objective is not to win, but to understand deeper. And to witness the moment illumination went wrong.");
hr();
    title("INTERACTION/GAMEPLAY","gameplay",2);
        leaf("An alternative approach to a classic text-based adventure, the player is without external senses or physical reality. Classic terms like LOOK and TAKE are replaced with congnative experiences like THINK and REMEMBER. Player does not have an INVENTORY, but rather a MEMORY where they collect the things they remember, unlocking the ability to THINK about concepts, and later CONSIDER concepts with one another.");
hr();
    title("SCREENSHOTS","screenshots", 2);
    skylite("<img src='". i_root ."/dani-leve/portfolio/tp_ss1.png' style='width:34%'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/tp_ss2.png' style='width:34%'> ");
close_section();

close_section();
close_section();

closeSky();

 ?>