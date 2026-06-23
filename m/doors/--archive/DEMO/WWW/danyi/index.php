<?php
SKY__AUTH(
    "DRL-SDK", "danyi", 
    "danyi", "danyi",
    "index", "danyi's demo of the pocket internet",
    
   /* FLAVOR */ "early-web"
);

quickDressing("header","
  text-shadow: 3px 3px 0 green;
  background-color:black;
  position;absolute;
  left:0;
  right:0;
");

quickDressing("datboi","
  position:absolute;
  bootom:20px;
  right:20px;
  scale: 2;
");

quickDressing("construction", "
  position:absolute;
  top:15px;
  right:5px;
");

quickDressing("wwwExplorer_innerShell", 
"font-size:1.2rem !important;
background: linear-gradient(0deg, green, purple) !important;
overflow-x: hidden !important;
");


openSky("remember me? its danyi.com");

  section("", "header");
    title("hi kittens!!", "title", 1);
  close_section();

makeLink("blog","Visit MY Blog");

  getDecor("I","underconstruction.gif", "", "construction");
 
  leaf("welcome to my website!!!!!
  thank you for being here!! sign my guestbook!!!!");

section("","datboi");
  getDecor("I","datboi.webp");
close_section();

hr();
getDecor("I","guestbook.gif");
h1("MY cuBOOK!");

section("","formContainer");
getTool("cuBOOK", "GuestPOST.DEMO");
close_section();
hr();
medHeading("Posts!!!!");
getTool("cuBOOK", "ViewCUs");

closeSky();
?>