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


$GLOBALS[$site]['room'] = [
                    ["name" => "songs"],
                    ];
$GLOBALS[$site]['key'] = "home"; 


$nav = [ "navSec" => 

    [ 
        "DOOR" => "songs", 
        "BUILDING" => "song_server", 
        "KEY" => "list", 
        "ROOMS" => [

            [ 
                "ROOM" => "upload_song", 
                "KEY" => "upload", 
            ],[ 
                "ROOM" => "song_list", 
                "KEY" => "list", 
            ],
    /* SECTION GROUP -------------------------------- */
    ]]
    ];

?>