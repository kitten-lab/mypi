

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
      ~                navigation config file              ~
--------------------------------------------------------------*/

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php'; 
$GLOBALS[BLOCK_ID]['GETS']['topNav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/top-nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "offices" ],
  [ "DOM" => "events" ],
  [ "DOM" => "offices" ],
  [ "DOM" => "offices" ],
]; 

$GLOBALS[BLOCK_ID]['NAV'] = [ 
  "navSec" => [
    "DOM" => "offices", 
    "BUILDING" => "Moon Offices", 
    "KEY" => "frontdesk", 
    "ROOMS" => [
      [ 
        "ROOM" => "The Front Desk", 
        "KEY" => "frontdesk", 
      ]
    ]
  ]
]; 

?>