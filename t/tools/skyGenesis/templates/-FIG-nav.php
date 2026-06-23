

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
                    ["DOM" => "public"],
                    ["DOM" => "{{DOM_SLUG}}"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; 

$nav = [ "navSec" => 
        [ 
        "DOM" => "public", 
        "BUILDING" => "public", //LABEL
        "PRIME_KEY" => "hi-from-SKY", 
        "ROOMS" => [
            [ 
                "ROOM" => "hi-from-SKY", //LABEL
                "KEY" => "hi-from-SKY", 
            ],
        ]
 ],[ 
        "DOM" => "{{DOM_SLUG}}", 
        "BUILDING" => "{{DOM_DISPLAY}}", 
        "KEY" => "home", 
        "ROOMS" => [ [
                "ROOM" => "{{KEY_SLUG}}", 
                "KEY" => "{{KEY_DISPLAY}}", 
            ],
        ]
 ]
 ]; ?>