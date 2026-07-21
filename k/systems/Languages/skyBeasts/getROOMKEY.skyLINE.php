<?php

// the sightsman prepares keys and directs to rooms:

function keyMaker() {
  if (empty($_GET)) {

    global $ENV;
      // Local vhost: DocumentRoot is already the SYS → pretty path /DOM/KEY only.
      global $ENV;
      $local = function_exists('mypi_env_is_local')
        ? mypi_env_is_local($ENV ?? '')
        : in_array($ENV ?? '', ['COMMANDCENTER9', 'ROSEWOOD8', 'LOCAL'], true);
      if ($local) { $localSLUG = ""; }
      else { $localSLUG = defined('BLOCK_URI') ? BLOCK_URI : ''; }

    $prettyURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($localSLUG !== '' && strpos($prettyURI, $localSLUG) !== false) {
      $parsed = trim(str_replace($localSLUG, '', $prettyURI));
    } else {
      $parsed = trim(str_replace($localSLUG, '', $prettyURI));
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
