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
                    ["DOM" => "NEWS"],
                    ["DOM" => "MUSIC"],
                    ["DOM" => "w"],
                    ];
$GLOBALS[$SITE]['key'] = "home"; //FOR LATER USE


$nav = [ "navSec" => 

    [ 
        "DOM" => "NEWS", 
        "BUILDING" => "HEADLINES", //nav label
        "PRIME_KEY" => "HEADLINES", 
        "ROOMS" => [

            [ 
                "KEY" => "Latest Posts",  //nav label
                "ROOM" => "HEADLINES",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],[ 
        "DOM" => "MUSIC", 
        "BUILDING" => "MUSIC", //nav label
        "PRIME_KEY" => "SONGS", 
        "ROOMS" => [

            [ 
                "KEY" => "Share Your Music!",  //nav label
                "ROOM" => "SONGS",  // key_name
            ],
    /* SECTION GROUP -------------------------------- */
    ]],
    ];

?>