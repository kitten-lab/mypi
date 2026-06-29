<?php
SKY__AUTH(
    "DRL-SDK", "danyi", 
    "programs", "WWW Programs",
    "testing", "weird tests",
    
   /* FLAVOR */ "early-web"
);

quickDressing("SHELF", "
  display: flex;
  flex-direction: row;
  gap: 12px;

  overflow-x: auto;
  overflow-y: hidden;

  flex-wrap: nowrap;

  align-items: flex-start;
  ");
quickDressing("wwwExplorer_innerShell", "
  background-color: blue;
");

openSky("danyi's plog");

bigHeading("what is this?");

shelf("myShelf");
//displayToy("MRA-001","Julie");
displayToy("MRA-001","ClassicBoi");
//displayToy("KCD-001","JULIE");
close_shelf();

section("mine", "");
  ROM_SCREEN();
close_section();
closeSky();