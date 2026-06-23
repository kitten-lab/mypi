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
                    ["DOM" => "resume"],
                    ["DOM" => "portfolio"],
                    ["DOM" => "contact"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; //FOR LATER USE

$nav = [ "navSec" => 

    [ 
        "DOM" => "portfolio", 
        "BUILDING" => "SYS", //nav label
        "PRIME_KEY" => "home", 
        "ROOMS" => [

            [ 
                "KEY" => "my pocket 'net",  //nav label
                "ROOM" => "home",  // key_name
            ],[ 
                "KEY" => "khaos detective engine",  //nav label
                "ROOM" => "tiles_Casework",  // key_name
            ],[ 
                "KEY" => "terminal prolog cli",  //nav label
                "ROOM" => "smh_terminalprolog",  // key_name
            ],
            /*[ 
                "KEY" => "something mattered here",  //nav label
                "ROOM" => "smh_forgettinghouse",  // key_name
            ]*/
    /* SECTION GROUP -------------------------------- */
    ]],[ 
        "DOM" => "resume", 
        "BUILDING" => "DOM", 
        "PRIME_KEY" => "home", 
        "ROOMS" => [

            [ 
                "KEY" => "resume", 
                "ROOM" => "home", 
            ],

            [ 
                "KEY" => "work experience",  //nav label
                "ROOM" => "experience",  // key_name
            ],

            [ 
                "KEY" => "case studies",  //nav label
                "ROOM" => "system",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],/*[ 
        "DOM" => "about", 
        "BUILDING" => "MOD", 
        "PRIME_KEY" => "me", 
        "ROOMS" => [

            [ 
                "KEY" => "about", 
                "ROOM" => "about", 
            ],

            [ 
                "KEY" => "contact",  //nav label
                "ROOM" => "contact",  // key_name
            ],
    ]],*/
    ];

?>