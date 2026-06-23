<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DRL", 
    /*MOD_DISPLAY*/  "Danielle Leve (Rudolph)", 
    
    /*DOM_SLUG*/     "portfolio", 
    /*DOM_DISPLAY*/  "portfolio",

    /*ROOM_SLUG*/    "tiles_Casework", 
    /*ROOM_DISPLAY*/  "Tiles: Detective: Case Work",

    /*ROOM_FLAVOR*/  "tiles_casework"
);

openSky('Portfolio for Danielle Leve');
section('background-image: url(' . i_root . '/dani-leve/portfolio/tiles_bg.png); background-repeat: no-repeat; background-position:top center; background-size:contain','case_study');
section('','case_study');
section('margin:20px;','case_study');
title("khaos detective engine","lead",1);
title("Chaos-Led Narrative Interpretation Instrument", "sublead",2);
hr();
leaf("[ <a href='#philo'>Philosophical POV</a> ] [ <a href='#narrative'>Narrative</a> ] [ <a href='#system'>System Overview</a> ] [ <a href='#mechanics'>Functional Mechanics</a> ] [ <a href='#prototype'>Prototype</a> ]");
hr();

title("Quick Handle:","handle",3);
leaf("An experimental game constrained to the most basic slot machine principles.
Randomly generated results are used to award weighted narrative fragments.
A single reel 'strip' is displayed like a keyboard, with the results shaped like piano chords.
Each winning combination plays the associated chord set alongside the weighted story fragments.");
title("Quick Purpose:","purpose",3);
leaf("Exploring the meaning made from the patterns of random outcomes.
");

hr();
section('width:68%; font-size:.8rem',"");
    title("Philosophical POV", "philo", 1);
        leaf("Humans are drawn to pattern seeking in everything around them, regardless of how random the dataset may appear to be. It is in our nature to search for faces in the whirls of trees, animals in the shapes of clouds, and beliefs about ourselves and the world around us in the random circumstances that become the stories of our lives.

        From randomness arises all meaning, all evolution, all sense of probability, possibility, and expectation. From randomness, we build the patterns used to navigate our choices, our interactions. From randomness, we produce certainty. From gambler's fallacy, we calcify our beliefs.
        
        KDE (Khaos Detective Engine) is a conceptual prototype game exploring the meaning found in the weight of random outcome. 
        ");
close_section();

section('width:68%; font-size:.8rem',"");
    title("The Flow State & The Slot Machine","flow", 2);
        leaf("The slot machine stands out remarkable for the sticky, attention-holding nature of its simple mechanics and probabilistic outcomes. Despite randomness and no certainty of winning, players spend their hard-earned money and limited free time seeking out chance and luck spinning the reels.

        By studying and gamifying the pattern-seeking drive, slot machines are designed to capture and hold the player's attention in an oscillating state of 'almost' completion and minor reward. In this conditioned flow state, the player finds as much reward in the near-misses as they do in the actual payouts. 

        This drip-reward/almost-but-not-quite cycle places the player in a deeper sub-conscious processing state where very little of their active cognitive functions are required or engaged. Each pull of the reels, the same physical behavior. Each result, the same conditioned seeking and matching of patterns. Each payout, the reinforcement of light and sound confirming the win. 

        The almosts - with their own lights, sounds, and probability - drive the moments between wins. Paced and structured, the player's mind is kept on the edge of winning. Gambler's fallacy makes meaning of the almost, and the engagement continues as expectancy rises.

        Observationally, slot machines are defined probability with a known house-edge and no continuity of chance. Each draw is an entirely new ask on the same math, the same art, the same potential. Randomness provides gambler's fallacy, narratives assigned to the patterns observed when random outcomes appear in smaller clusters.");
close_section();


hr();
    title("NARRATIVE", "narrative", 1);
    section('width:60%; font-size:.8rem',"");
        leaf("Detective K.D. Moire has been handling cases in this town for longer than she can remember. 
        
        <strong>The problem is, Detective K.D. Moire can't remember anything, anymore. </strong>

        Each day the stories loop. The same clients return. The same business cards arrive.
        Each day, K.D. Moire believes she investigates, discovers, and closes the cases. 
        But the stories never change, returning again and again with only the slightest shift in the weight and sound of each blue.

        What meaning can be made when the same stories loop and loop, but the weight of their meaning changes each time?");
    close_section();

hr();
    title("SYSTEM OVERVIEW", "system", 1);
    section('width:60%; font-size:.8rem',"");
        leaf("This is a prototype where I am exploring how slot machine mechanics can be used to generate narrative meaning instead of just payouts. Instead of winning money, the player earns fragments - atmosphere shifts, clues, and weighted signals - that slowly build into a story.
        
        I wanted to test whether the same psychological loops used in slot machines and freemium systems could be redirected toward meaning-making instead of pure reward extraction.");
    close_section();

    title("FUNCTIONAL MECHANICS", "mechanics", 2);
    section('width:60%; font-size:.8rem',"");
        leaf("Simple slot mechanics with no bonus features. You begin in the Office of a detective. At the first win, a case will begin. Each subsequent win on the game will progress the story through fragmented narrative pieces.
        
        - Low paying symbols shift the ATMOSPHERE
        - Medium paying symbols offer clues
        - High outcome symbols add emphasis and weight to a clue
        - Special outcome symbols heavily reinforce or prioritize certain narrative elements");
    close_section();

hr();
    title("PROTOTYPE", "prototype", 1);
        getTool("tiles","InstrumentalStories");    
    close_section();

close_section();

closeSky();

 ?>