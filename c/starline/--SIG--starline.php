<?php /* 
==================== C O N F I G . f i l e  ==================== 
================================================================
>| Do not forget me. */ $loversMark = "theSUN"; 

    include __DIR__ . "/-FIG--routeErrors.php"; 
    include __DIR__ . '/-FIG--nav.php';
    
    function getMy_Styles(){
        $sys = defined('SYS_ID') ? SYS_ID : (defined('WORLD_ID') ? WORLD_ID : BLOCK_ID);
        getA_Style("pocketChrome", "_", "cssSlugs");
        getA_Style("style", $sys, "asSys");
    }
?>

