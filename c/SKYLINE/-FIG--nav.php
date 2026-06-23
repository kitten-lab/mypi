

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
~                terminal navigation config file              ~
----------------------------------------------------------------
Listen, you are going to need to TRUST THE [] colors. They 
don't lie. But sometimes, you will be confused by this nest.
That's okay. Each time it WILL GET EASIER.  -abl 
--------------------------------------------------------------*/

$GLOBALS[$SITE]['GETS']['sideNav'] = $GLOBALS['SONAR'] . 'a/' . $SYS . '/asSys/nav.php'; 
$GLOBALS[$SITE]['GETS']['topNav'] = $GLOBALS['SONAR'] . 'a/' . $SYS . '/asSys/top-nav.php'; 

$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "home"],
                    ["DOM" => "offices"],
                    ["DOM" => "w"]
                    ]; 
$GLOBALS[$SITE]['key'] = "home"; 

$nav = [ "navSec" => 

    [ 
        "DOM" => "offices", 
        "BUILDING" => "PUBLIC OFFICES", //DOM?
        "KEY" => "frontDesk", 
        "ROOMS" => [

            [ 
                "ROOM" => "RECEPTION DESK", 
                "KEY" => "frontDesk", 
            ],
            [ 
                "ROOM" => "SKY DESK REPORTS", 
                "KEY" => "news", 
            ],
    /* SECTION GROUP -------------------------------- */
    ]],
 ]; ?>