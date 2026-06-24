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
                    ["DOM" => "soper"],
                    ["DOM" => "adm-ka"],
                    ["DOM" => "nim-ka"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; //FOR LATER USE


$nav = [ "navSec" => 

    [ 
        "DOM" => "soper", 
        "BUILDING" => "soper-readme", //nav label
        "PRIME_KEY" => "README", 
        "ROOMS" => [

            [ 
                "KEY" => "soper-reminders",  //nav label
                "ROOM" => "reminders",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],[ 
        "DOM" => "adm-ka", 
        "BUILDING" => "adm-ka", 
        "PRIME_KEY" => "README", 
        "ROOMS" => [

            [ 
                "KEY" => "adm-overview", 
                "ROOM" => "adm-overview", 
            ],

            [ 
                "KEY" => "adm-intera-network",  //nav label
                "ROOM" => "adm-intera-network",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],[ 
        "DOM" => "nim-ka", 
        "BUILDING" => "nim-ka", 
        "PRIME_KEY" => "README", 
        "ROOMS" => [

            [ 
                "KEY" => "nim-overview", 
                "ROOM" => "nim-overview", 
            ],
    /* SECTION GROUP -------------------------------- */
    ]],
    ];

?>