<?php 
SKY__AUTH(
    /*MOD_SLUG*/     "DRL", 
    /*MOD_DISPLAY*/  "Danielle Leve (Rudolph)", 
    
    /*DOM_SLUG*/     "portfolio", 
    /*DOM_DISPLAY*/  "portfolio",

    /*ROOM_SLUG*/    "home", 
    /*ROOM_DISPLAY*/  "portfolio home",

    /*ROOM_FLAVOR*/  "skyline-standard"
);

openSky('Portfolio for Danielle Leve');
section('','case_study');
section('margin:20px;','case_study');
    title("SILO: MY POCKET INTERNET" , "lead", 1);
    title("AN INTERNET-SHAPED PERSONAL DATA AND CONTENT MANAGEMENT SYSTEM", "sublead", 2);

hr();
leaf("<H4>QUICK JUMP</H4> [ <a href='#surfaces'>Example Surfaces</a> ] [ <a href='#system'>Features Overview</a> ] [ <a href='#quickmore'>Additional System Details</a> ] [ <a href='#philo'>The Need</a> ]");
hr();

title("Quick Handle:","handle",3);
leaf("A unique data management system that acts as a small pocket internet.
Driven on quickly producible web surfaces all sharing a linked toolset and architecture. 
Each surface can be accessed via the internet, and tools may be placed on pages via a simple custom DSL. 

Make a page, ingest a note, remove the post tool, and voila! 

Your data has been crated in a shared json data format, distributed across an array of simple paper reports, 
and now exists as a surface artifact you can remember contextually by the look and feel of where it was placed.");
title("Quick Purpose:","purpose",3);
leaf("An expressive and structured content and data-management system powered by PHP and JSON.
");


hr();
    title("Example Surfaces","surfaces",1);

    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_hero.png' style='width:80%'> ");
hr();
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_skyline.png' style='width:34%'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_bigbox.png' style='width:31%'> ");
    skylite("<img src='". i_root ."/dani-leve/portfolio/silo_terminal.png' style='width:32.5%'> ");
    hr();

section('width:68%;',"");
    title("Features Overview", "system", 1);
leaf("<H4>ROUTE DETAILS</H4>[ <a href='#engine'>Aleph Bet Router</a> ] [ <a href='#surfacing'>Route A: Surfaces</a> ] [ <a href='#tools-toys'>Route K: Tools & Toys</a> ] ");
leaf("<H4>DETAILED FEATURES</H4>[ <a href='#data-storage'>Chester's Imports: Data Storage</a> ] [ <a href='#charlie'>CharlieTHREADS: Tag System</a> ] ");
hr();
    title("The Aleph Bet Router: A Decentralized Render-Engine","engine",2);
        leaf("Each aspect of the page rendering process is partitioned into provinces. 
        The routes are letter coded, each responsible for handling a specific aspect of the SILO architecture.
    <ul>
        <li>A - To styles, shells, and common page includes for the surfaces</li>
        <li>B - Front-facing credential files (indicated where to route to call the requested pages)</li>
        <li>C - To configurations, nav settings, and custom signature files unique to each surface</li>
        <li>D - To the data storage. All paper reports and json file 'crates' are stored here.</li>
        <li>K - Master tool kits, systems and configuration files usable across surfaces</li>
        <li>I - To images and other importable resources for surface rooms</li>
        <li>To maps, the location where 'rooms' are stored (the surface page's contents)</li></ul>
    All routing begins at the index.php in each surface's B folder. 
    
    Signature and Authorization files declare the names of each layer of the system, calling forward each piece required to pull together and render the surface.
    
    ");
    title("ROUTE A: Surface Identities & Contextual Memory Recall","surfacing",2);
    leaf("Memory is contextual, often storing with the sights, sounds, and environmental cues baked in. When studying, researching, or just trying to remember, we find there is a limited amount of storage possible without a change in context or environment to expand the space for more memories.
    
    My Pocket Internet uses uniquely designed and formatted surfaces with distinguishable 'personalities' to produce the sense of environment and other contextual cues to assist in the ability to recall where any given piece of material is stored, and to recall it more rapidly without actively engaging it just by 'remembering' the sense of the surface it is located on.
    
    ");
    hr();
    title("Route K: Integratable Tools, Assistants and Toys","tools-toys",2);
    leaf("A set of tools and toys can be ingested onto any room(page) on the system using skylite's getTool(); function.
These tools and toys enable a wider array of features and functionalities to the surfaces.
These include a variety of ingestion tools and display pages:
<blockquote>
<h2 id='basic'>Basic Ingestors</h2><blockquote>
<strong>postBASIC: </strong>  A basic blog-like tool for ingesting 'post' data and displaying it on the surface.
<ul><li>Payload: 'post' => agent, topic, content</li><li>Modes: MakePost, ViewList, ViewPost, SoperView, Headerlines</li></ul>
<strong>soprBASIC: </strong> Import fragments by topic. Automatically sorts and numbers by section.
<ul><li>Payload: 'post' => agent, section, fragment</li><li>Modes: AddFragment, ViewList (Coming Soon: SortFragments)</li></ul>
<strong>reportBASIC:</strong> Allows a post to be submitted by a named author, directed towards a surface.
<ul><li>Payload: 'report' => agent, topic, content, fromUser</li><li>Modes: MakePost, ViewList, ViewPost</li><li>Planned: Duplicate crate delivery to referenced surfaces in the report</ul>
<strong>mailroomBASIC: </strong> Allows for the to/from emailing between surface environments
<ul><li>Payload: 'mail' => to, from, topic, content</li><li>Modes: SendMail, ViewInbox, ViewOutbox</li></ul></blockquote>
<h2 id='addtools'>Additional Tools</h2><ul><li><strong>JUKEBOX</strong>: Allows for the collecting of songs and their tags, occurances, and surfaces used when it played.</li>
<li><strong>CONCORD</strong>: Coming soon, a tool for collecting definitions and correlations.</li>
<li><strong>skyGENESIS:</strong> A tool for launching new surfaces from any surface.</li>
<li><strong>keyMAKER2:</strong> A tool for creating roomkeys (website pages) in skylite and launching them from the surface</li>
<li><strong>jsonREADER:</strong> A simple reader for json files. Can display readable content on any surface from json file formats.</li>
<li><strong>mdREADER:</strong> A simple reader for md files. Can display readable content on any surface from markdown file formats.</li>
<li><strong>echoREADER:</strong> A viewport tool for viewing all of the activity system wide in various report formats.</li>
<li><strong>pianoREADER: </strong>Run any fragments through the pianoREADER to randomize the narrative weight of meaning</li>
</blockquote>");
    hr();
    title("Chester's Imports: Crates! A Normalized {JSON} Data Structure","data-storage",2);
    leaf("Each ingestor tool, including creation tools, creates a normalized json nest called a CRATE. 

    Each crate is given a unique CUID and stored in locations based on posting surface, cross-surface delivery, and long term data storage, both by ingested time and any provided event time.
    
    Each crate contains the following content:
    <ul><li>CUID: Unique crate ID generated at time of creation</li>
    <li>Assistant metadata about the used Tool and its versioning information</li>
    <li>Payload: The unique payload for the specific Assistant Tool</li>
    <li>Route: The route, when a crate was create via a cross-surface ingestor</li>
    <li>charlieTHREADS: The parsed and nested tags from charlieTAGS</li>
    <li>Tag Metadata: The versioning for the tag parser and the raw tag string</li>
    <li>Environment: The details of the surface environment location ingestion occurred</li>
    <li>Source: Space for storing the detailed metadata for origin material location (ie importing from Obsidian or Evernote)</li>
    <li>TPS data: Unix timestamps for both ingested time and event time</li></ul>
    ");
hr();
    title("charlieTHREADS: A Multipositional Tagging System","charlie",2);
    leaf("named after charlie from always sunny in the episode where he finds himself working in the mailroom, 
    charlieTHREADS is a structured tagging system derived from a light, loose form custom DSL. 
    
    it allows for the ease of tracking entities, relationships, events and whatever else you feed into it, 
    over time and narrative weight (usage count). instead of simple tags, each thread capture who recorded what, when, 
    and the context of the material it was recorded in.
    
    charlieTHREADS turns tagging into a temporal, relational system.

    <ul><li>preserves history instead of overwriting state</li><li>allows multiple perspectives (who reported what)</li><li>enables time-based queries (first mention, last mention, frequency)</li><li>supports conflicting or evolving truths</li></ul>
this allows SILO to function less like a notes app and more like a living data network.

");
   
hr();

title("Additional System Details","quickmore",1);
leaf("SILO also includes:
<ul><li>temporal clustering reports</li>
<li>fragmented-event reconstruction and recording</li>
<li>relational entity tracking</li>
<li>cross-surface chonology analysis</li>
<li>weighted narrative reporting</li>
<li>nuanced paper reports for tag relationships</li>
<li>contextual timeline synthesis</li></ul>
");
hr();
    title("The Story of the Needing Something", "philo", 1);

    leaf("Over the years, I have collected a lot of data. 
Thoughts, poems, story ideas, systems, theories, research, and more. 

Without some type of organization and unification, my data had become a near inaccessible, sprawling nightmare to manage.
My Pocket Internet is my solution to that problem.

So many times I found myself writing the same material over: 
    - feedback for production teams,
    - itemized lists, 
    - budgets, 
    - story-starters,
    - and more. 

In a pinch, without the ability to locate my data, I would reproduce again and again that which I had, somewhere, stored already.

An organizer at heart, I was constantly trying new tools, searching anything to help hold, manage, and cross-connect all of the many fragments of data I had captured over dozens of years. 

I was a writer, an artist, a philosopher, a mother, a production executive. 
Every facet of my thinking had different needs and contexts for storing their data.

Over the decades, I explored everything. I tried databases (too impersonal), todo lists (too singular), simple notes apps (too sprawling), Obsidian (I crashed it). I even installed Rukovoditel, a project-management software DEVELOPMENT application (too... stiff). 

Year after year, try after try, each tool fell short in one or more of the needs I found myself having.

I wanted a platform where all of my notes connected like entities, and time and connection was all threaded, tagged, and tracked over context and stored universally, in a way I could later derive deeper meaning found between the edges of the data, via reports and catalog lookups.

I hadn't worked in PHP before, but I needed something! I started building a framework:<h2>the pocket internet</h2>a data system that acts as a small pocket internet, driven on quickly producible web surfaces all sharing a linked toolset and architecture. Each surface can be accessed via the internet, and tools may be placed on pages via a simple custom DSL. Make a page, ingest a note, remove the post tool, and viola! Your data has been crated in a shared json data format, distributed across an array of simple paper reports, and now exists as a surface artifact you can remember contextually by the look and feel of where it was placed.");
    
close_section();
close_section();
close_section();

closeSky();

 ?>