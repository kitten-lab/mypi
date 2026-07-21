<?php
global $MAP;

SKY__AUTH(
    "modbsg", "daniel wake", 
    "private", "privateSPACE",
    "betsoft-todo", "My To Dos",
    
   /* FLAVOR */ "skyline-standard"
);
quickDressing("wwwExplorer_innerShell","
  background-color: #333; font-size: 1.4rem;
");
quickDressing("wwwExplorer_innerShell a","
  color: red;
");
openSky("Its okay if you hate your job sometimes");
section('', "section_container");
    section('', "fragments");
        medHeading(defined('ROOM_DISPLAY') ? ROOM_DISPLAY : 'My To Dos');
        getTool("soprBASIC", "ViewList");
    close_section();
    section('','inputs');
        medHeading("soprBASIC");
        getTool("soprBASIC", "AddFragment");
    close_section();
close_section();
closeSky();