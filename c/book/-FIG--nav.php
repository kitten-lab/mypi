

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
      ~                navigation config file              ~
--------------------------------------------------------------*/

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . BLOCK_ID . '/asSys/nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "terminal_girls" ],
  [ "DOM" => "tavern" ],
]; 

$GLOBALS[BLOCK_ID]['NAV'] = [ 
  "navSec" => [ 
    "DOM" => "terminal_girls", 
    "BUILDING" => "Terminal Girl Books", 
    "KEY" => "home", 
    "ROOMS" => [
      [ 
        "ROOM" => "Oriel", 
        "KEY" => "oriel", 
      ],[ 
        "ROOM" => "Kat", 
        "KEY" => "kat", 
      ],[ 
        "ROOM" => "Sam", 
        "KEY" => "sam", 
      ],
    ]
  ],
]; 

?>