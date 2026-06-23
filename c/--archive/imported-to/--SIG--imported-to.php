<?php /* 
==================== C O N F I G . f i l e  ==================== 
================================================================
>| Do not forget me. */ $loversMark = "The Cathedral Girl"; 

    include __DIR__ . '/-FIG--nav.php';
    include __DIR__ . "/-FIG--routeErrors.php"; 
    
    function getMy_Styles(){
    $SITE = $GLOBALS['SITE'];
        getA_Style("style", $GLOBALS[$SITE]['SYS'], "asSys");
        getA_Style("sky",   $GLOBALS[$SITE]['SYS'], "asSys");
        getA_Style("fonts", $GLOBALS[$SITE]['SYS'], "asSys");
        getA_Style("style", $GLOBALS[$SITE]['DOM'], "asDom");

    }
?>

