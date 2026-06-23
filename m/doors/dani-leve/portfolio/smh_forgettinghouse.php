<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DRL", 
    /*MOD_DISPLAY*/  "Danielle Leve (Rudolph)", 
    
    /*DOM_SLUG*/     "portfolio", 
    /*DOM_DISPLAY*/  "portfolio",

    /*ROOM_SLUG*/    "smh_terminalprolog", 
    /*ROOM_DISPLAY*/  "Silo Terminal Prolog",

    /*ROOM_FLAVOR*/  "smh_forgettinghouse"
);

openSky('Portfolio for Danielle Leve');
section('','case_study');
    skylite("<img src='". i_root ."/dani-leve/portfolio/smh_forgettinghouse.png' style='width:100%'> ");
section('margin:20px;','case_study');
    medHeading("SOMETHING MATTERED HERE");
    bigHeading("THE FORGETTING HOUSE");
    medHeading("Over the years, I have collected a lot of data. Thoughts, poems, story ideas, systems, theories, research, and more. Strewn across the landscapes of Evernote, Google Docs, paper journals, and now over 500 distinct chat logs with an AI agent, my data had become a near inaccessable, sprawling nightmare to manage.");
    leaf("<h3>So many times I found myself writing the same material over: feedback for production teams, itemized lists, budgets, story-starters. I would reproduce again and again that which I had, somewhere, stored already.<br><br>An organizer at heart, I was constantly trying new tools, searching anything to help hold, manage, and cross-relate all of the many fragments of data I had captured over dozens of years. I was a writer, an artist, a philosopher, a mother, a production executive. Every facet of my thinking had different needs and contexts for storing their data.<br><br>Over the decades, I explored everything. I tried databases (too impersonal), todo lists (too singular), simple notes apps (too sprawling), Obsidian (I crashed it). I even installed Rukovoditel, a project-management software DEVELOPMENT application (too... stiff). Year after year, try after try, each tool fell short in one or more of the needs I found myself having.</h3>
I wanted a platform where all of my notes connected like entities, and time and connection was all threaded, tagged, and tracked over context and stored universally, in a way I could later derive deeper meaning found between the edges of the data, via reports and catalog lookups.

I hadn't worked in PHP before, but I needed something! I started building a framework:<h2>the pocket internet</h2>a data system that acts as a small pocket internet, driven on quickly produable web surfaces all sharing a linked toolset and architecture. Each surface can be accessed via the internet, and tools may be placed on pages via a simple custom DSL. Make a page, ingest a note, remove the post tool, and viola! Your data has been crated in a shared json data format, distributed across an array of simple paper reports, and now exists as a surface artifact you can remember contextually by the look and feel of where it was placed.");
    
    medHeading("Example Surfaces");
close_section();

    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_skyline.png' style='width:34%'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_bigbox.png' style='width:31%'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_terminal.png' style='width:32.5%'> ");
close_section();

closeSky();

 ?>