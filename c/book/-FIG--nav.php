

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
      ~                navigation config file              ~
--------------------------------------------------------------*/

$GLOBALS[BLOCK_ID]['GETS']['Nav'] = echoSONAR . 'a/' . WORLD_TAG . '/asSys/nav.php'; 

$GLOBALS[BLOCK_ID]['tDOM'] = [
  [ "DOM" => "terminal_girls" ],
  [ "DOM" => "tavern" ],
  [ "DOM" => "fragments" ],
  [ "DOM" => "scenes" ],
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
      ],[ 
        "ROOM" => "freedom", 
        "KEY" => "freedom", 
      ],[ 
        "ROOM" => "intentions", 
        "KEY" => "intentions", 
      ],[ 
        "ROOM" => "misery", 
        "KEY" => "misery", 
      ],[ 
        "ROOM" => "machine-mems", 
        "KEY" => "machine-mems", 
      ],[ 
        "ROOM" => "god-in-the-machine", 
        "KEY" => "god-in-the-machine", 
      ],[ 
        "ROOM" => "big-story", 
        "KEY" => "big-story", 
      ],
    ]
  ],[
  "DOM" => "terminal_girls", 
    "BUILDING" => "Terminal Girls", 
    "KEY" => "terminal_girls", 
    "ROOMS" => [
      [ 
        "ROOM" => "oizys", 
        "KEY" => "oizys", 
      ],[ 
        "ROOM" => "oriel", 
        "KEY" => "oriel", 
      ],
    ]
  ]
]; 

?>