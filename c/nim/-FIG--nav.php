

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
      ~                navigation config file              ~
--------------------------------------------------------------*/

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "elog" ],
  [ "DOM" => "archetypes" ],
]; 

$GLOBALS[BLOCK_ID]['NAV'] = [ 
  "navSec" => [ 
    "DOM" => "elog", 
    "BUILDING" => "The Wire Logs", 
    "KEY" => "index", 
    "ROOMS" => [
      [ 
        "ROOM" => "Index", 
        "KEY" => "index", 
      ],
    ]
  ],
]; 

?>