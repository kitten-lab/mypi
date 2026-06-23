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


$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "personal-log"],
                    ["DOM" => "archived"],
                    ["DOM" => "mystery"],
                    ["DOM" => "programs"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; 

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
        ]
 ]
 ]; ?>