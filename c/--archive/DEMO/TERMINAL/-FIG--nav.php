

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
                    ["DOM" => "public"],
                    ["DOM" => "run"],
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
                "ROOM" => "hi.MSG: ALT001", //LABEL
                "KEY" => "hi-from-SKY", 
            ],
        ]
 ],[ 
        "DOM" => "run", 
        "BUILDING" => "run", 
        "KEY" => "home", 
        "ROOMS" => [ [
                "ROOM" => "terminal.prolog", 
                "KEY" => "prolog", 
            ],[
                "ROOM" => "terminal.connector", 
                "KEY" => "connector", 
            ],
        ]
 ]
 ]; ?>