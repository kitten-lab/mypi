<?php

// the sightsman prepares keys and directs to rooms:

function keyMaker() {
  if (empty($_GET)) {

    global $ENV;
      if ($ENV === "ROSEWOOD8"){ $localSLUG = ""; }
      else { $localSLUG = ""; }

    $prettyURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (strpos($prettyURI, $localSLUG) !== false) {    
      $parseForROSEWOOD = str_replace($localSLUG, '', $prettyURI);
      $parsed = trim($parseForROSEWOOD);
    } else {    
      $parseForPUBLIC = str_replace(BLOCK_URI . "/", '', $prettyURI);
      $parsed = trim($parseForPUBLIC);
    }

    $uriFRAGS = explode('/', $parsed);
    
    global $room;
      $room = $uriFRAGS[1];
      $key = $uriFRAGS[2] ?? null;

    global $fetch;
      $fetch = $uriFRAGS[3] ?? null;

        $_GET[$room] = $key;
  }
}



function lockAndKey(){  
    global $SITE;

    $foundKey = false;
    $foundRoom = false;
    if (empty($_GET)) {
            notARoom();
            require resolveShell();
            exit;
        }
    foreach ($_GET as $room => $key) {
        $doors = $GLOBALS[$SITE]['tDOM'] ?? [];
        
        foreach ($doors as $door){
            if ($room == $door['DOM']) {
                $foundRoom = true;
                $path = ROOM_ROUTE . '/' . $door['DOM'] .'/' . $key . '.php';
                if (empty($key)) {
                    aRoomWithNoKey();
                    require resolveShell();
                    exit;
                }
                    if (file_exists($path)) {
                        $foundKey = true;
                        require $path;
                        break;
                    } 
                    break;
            } 
        }
    }
        if (!$foundRoom) { notARoom(); }
        if (!$foundKey && $foundRoom) { noKeyFound(); }
        if (!$foundKey && !$foundRoom) { noKeyFound(); }

    require resolveShell();
}

function interraLocation(){
    // retired, remove from known usage locations
}
