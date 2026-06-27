

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

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . WORLD_ID . '/asSys/nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "publicOffices" ],
  [ "DOM" => "reportDepartment" ],
]; 

$nav = [ "navSec" => 

    [ 
        "DOM" => "publicOffices", 
        "BUILDING" => "PUBLIC OFFICE", //DOM?
        "KEY" => "frontDesk", 
        "ROOMS" => [

            [ 
                "ROOM" => "RECEPTION DESK", 
                "KEY" => "frontDesk", 
            ],
            [ 
                "ROOM" => "SKY DESK REPORTS", 
                "KEY" => "news", 
            ],
    /* SECTION GROUP -------------------------------- */
    ]],[ 
        "DOM" => "reportDepartment", 
        "BUILDING" => "REPORT DEPARTMENT",  
        "KEY" => "frontDesk", 
        "ROOMS" => [

            [ 
                "ROOM" => "RECEPTION DESK", 
                "KEY" => "frontDesk", 
            ],

        [ 
            "ROOM" => "REPORT AN OMEN", 
            "KEY" => "omansOmens", 
            ],
        [ 
            "ROOM" => "REPORT SENSE OF HYMN", 
            "KEY" => "songOfSongs", 
            ],
        [ 
            "ROOM" => "REPORT A SECRET KNOWN", 
            "KEY" => "teeHeeSecrets", 
            ],
    /* SECTION GROUP -------------------------------- */
    ]]
 ]; ?>