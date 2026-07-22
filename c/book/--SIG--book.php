<?php /* 
==================== C O N F I G . f i l e  ==================== 
================================================================
>| Do not forget me. */ $loversMark = "theSKY"; 

    include __DIR__ . "/-FIG--routeErrors.php"; 
    include __DIR__ . '/-FIG--nav.php';
    
    function getMy_Styles(){
        // Path segment must be SYS id (book), not display WORLD_TAG.
        $sys = defined('SYS_ID') ? SYS_ID : (defined('WORLD_ID') ? WORLD_ID : BLOCK_ID);
        // Shared pocket chrome (scrollbars / pane tokens) before surface paint
        getA_Style("pocketChrome", "_", "cssSlugs");
        getA_Style("style", $sys, "asSys");
    }
?>

