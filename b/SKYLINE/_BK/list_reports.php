<?php 
require_once '_configs/clrRoutes.php';
openSky("VIEWING PUBLIC_USER REPORTS");
nameSelf("REPORTER");
getTool("plogBasic","List");
closeSky();
require resolveShell($sys);
?>