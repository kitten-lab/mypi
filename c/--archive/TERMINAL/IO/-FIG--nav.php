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



$GLOBALS[$SITE]['GETS']['navCall'] = $GLOBALS['SONAR'] . 'a/' . $SITE . '/asSys/nav.php'; 

$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "COMMS"],
                    ["DOM" => "DAY_INVENTORY"],
                    ["DOM" => "SDK-808"],
                    ["DOM" => "w"]
                    ]; 
$GLOBALS[$SITE]['KEY'] = "home"; 

$nav = [ "navSec" => 
[ 
        "DOM" => "SDK-808", 
        "BUILDING" => "USER_MENU", //nav label
        "PRIME_KEY" => "IMPORTS", 
        "ROOMS" => [
            [ 
                "KEY" => "IMPORTS",  //nav label
                "ROOM" => "IMPORTS",  // key_name
            ],[ 
                "KEY" => "EXPORTS",  //nav label
                "ROOM" => "EXPORTS",  // key_name
            ],
        ]
 ], [ 
        "DOM" => "SDK-808", 
        "BUILDING" => "DAY_INVENTORY", //nav label
        "PRIME_KEY" => "SDK-808", 
        "ROOMS" => [
            [ 
                "KEY" => "POST",  //nav label
                "ROOM" => "POST",  // key_name
            ],[ 
                "KEY" => "POST-REVIEW",  //nav label
                "ROOM" => "POST-REVIEW",  // key_name
            ],
        ]
 ],[ 
        "DOM" => "SDK-808", 
        "BUILDING" => "SAM_SECTION", //nav label
        "PRIME_KEY" => "SDK-808", 
        "ROOMS" => [
            [ 
                "KEY" => "MUSIC",  //nav label
                "ROOM" => "MUSIC",  // key_name
            ],[ 
                "KEY" => "ALEPH-BET",  //nav label
                "ROOM" => "ALEPH-BET",  // key_name
            ],[ 
                "KEY" => "UNSORTED",  //nav label
                "ROOM" => "UNSORTED",  // key_name
            ],
        ]
 ]
    ]; ?>