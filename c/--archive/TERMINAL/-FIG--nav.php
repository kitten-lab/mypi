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
global $SITE;


$GLOBALS[$SITE]['GETS']['navCall'] = $GLOBALS['SONAR'] . 'a/' . $SITE . '/asSys/nav.php'; 

$GLOBALS[$SITE]['tDOM'] = [
                    ["DOM" => "ROOT"],
                    ["DOM" => "w"]
                    ]; 
$GLOBALS[$SITE]['key'] = "home"; 

$nav = [ "navSec" => 
   
 [ 
        "DOM" => "ROOT", 
        "BUILDING" => "ROOT", //nav label
        "PRIME_KEY" => "home", 
        "ROOMS" => [
            [ 
                "KEY" => "SAM.TXT",  //nav label
                "ROOM" => "SAM.TXT",  // key_name
            ],
        ]
 ]
 ]; ?>