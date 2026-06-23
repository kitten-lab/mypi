<?php


function getTool($tool, $function) {
  $GLOBALS['GETS']['set'][] = function() use ($tool, $function) { 
    getToolNIM($tool, 'page', $function);
    };
  $GLOBALS['GETS']['actor'][] = function() use ($tool, $function) {
    getToolNIM($tool, 'actor', $function);
    };
  $GLOBALS['GETS']['scripts'][] = function() use ($tool, $function) {
    getToolNIM($tool, 'script', $function);

    };
    $GLOBALS['GETS']['dressing'][] = function() use ($tool) {
      loadTool_Style($tool);
    };
}


function getToolNIM($tool, $set, $function){
    $file = $GLOBALS['SONAR'] . "t/tools/" . $tool . "/" . $set . $function . ".php";
    if (is_file($file)) {
    loadTool($tool, $set, $function);
    } else {
        Console_Log_Warning($tool . " " . $function . " " . $set . ": Not found. Is it even an Error?");
        Console_Log_Note("[ NOTE TO SELF ] Adapt error checking later to ensure we don\'t spit errors out for intentionally missing elements.");
    }
}


function loadTool_Style($tool) {
    $path = "/tools/" . $tool . '/' . $tool . ".css";
    $full = $GLOBALS['SONAR'] . "t" . $path;
    if (is_file($full)) {
         echo '<link rel="stylesheet" type="text/css" href="' . t_root . $path . '">';
         } else {
            Console_Log_Warning($tool . " MINOR ERROR: Style MISSING FROM: " . $tool . " " . $path );
         }
}


    function loadTool($tool, $type, $function) {
        $result = $GLOBALS['SONAR'] . 't/tools/' . $tool . '/' . $type . $function . '.php';
        if (is_file($result)) {
            include $result;
        } else {
            error_log("KDE! Tool file not found. " . $result);
        }  
    }