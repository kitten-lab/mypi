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


$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "symbolCRAFT"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; //FOR LATER USE


$nav = [ "navSec" => 

    [ 
        "DOM" => "symbolCRAFT", 
        "BUILDING" => "symbolCRAFT", //nav label
        "PRIME_KEY" => "home", 
        "ROOMS" => [
            [ 
                "KEY" => "business plan",  //nav label
                "ROOM" => "business-plan",  // key_name
            ],
            [ 
                "KEY" => "games/detective",  //nav label
                "ROOM" => "[slots]=detective-case_work",  // key_name
            ],
            [ 
                "KEY" => "--view",  //nav label
                "ROOM" => "view-business-plan",  // key_name
            ],
            [ 
                "KEY" => "cube engine",  //nav label
                "ROOM" => "cube-engine",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],
    ];

?>