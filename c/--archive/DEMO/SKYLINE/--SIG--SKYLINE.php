<?php /* 
==================== C O N F I G . f i l e  ==================== 
================================================================
>| Do not forget me. */ $loversMark = "theSKY"; 
    include __DIR__ . '/-FIG--nav.php';
    include __DIR__ . "/-FIG--routeErrors.php"; 
    
    function getMy_Styles(){
        getA_Style("style", WORLD_ID, "asSys");
        getA_Style("sky",   WORLD_ID, "asSys");
    }
    
?>

