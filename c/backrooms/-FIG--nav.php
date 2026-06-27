

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
      ~                navigation config file              ~
--------------------------------------------------------------*/

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . WORLD_TAG . '/asSys/nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "requests" ],
  [ "DOM" => "tavern" ],
  [ "DOM" => "fragments" ],
]; 

$GLOBALS[BLOCK_ID]['NAV'] = [ 
  "navSec" => [
    "DOM" => "fragments", 
    "BUILDING" => "Fragment Sheets", 
    "KEY" => "home", 
    "ROOMS" => [
      [ 
        "ROOM" => "connection", 
        "KEY" => "connection", 
      ]
    ]
  ]
]; 

?>