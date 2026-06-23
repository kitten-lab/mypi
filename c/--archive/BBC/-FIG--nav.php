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
$GLOBALS[$SITE]['GETS']['sideNav'] = $GLOBALS['SONAR'] . 'a/' . $SITE . '/asSys/nav.php'; 


$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "GDD"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; //FOR LATER USE


$nav = [ "navSec" => 

    [ 
        "DOM" => "GDD", 
        "BUILDING" => "GAME DESIGN HOUSE", //nav label
        "PRIME_KEY" => "README", 
        "ROOMS" => [

            [ 
                "KEY" => "README",  //nav label
                "ROOM" => "README",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],
    ];

?>