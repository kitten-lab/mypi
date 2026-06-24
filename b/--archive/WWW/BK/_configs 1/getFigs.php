<?php
    require_once $sonar . $kinit . "callImages.php";
    require_once $sonar . $kinit . "callSky.php"; 
    include 'figs/fig-logBasic.php';
    
    function getMy_Styles(){
        getA_Style("fonts",$GLOBALS['sys'],"asSys");
        getA_Style("style",$GLOBALS['sys'],"asSys");
        getA_Style("sky",$GLOBALS['sys'],"asSys");

    }
?>