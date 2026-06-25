<?php

function SKY__AUTH(
    string $MOD_SLUG, 
    string $MOD_DISPLAY, 
    string $DOM_SLUG, 
    string $DOM_DISPLAY, 
    string $ROOM_SLUG, 
    string $ROOM_DISPLAY,
    ?string $ROOM_FLAVOR="classic"
    ) {
        global $SITE;

        $GLOBALS[$SITE]['MOD_SLUG'] = $MOD_SLUG;
        $GLOBALS[$SITE]['MOD_DISPLAY'] = $MOD_DISPLAY;
        $GLOBALS[$SITE]['DOM_SLUG'] = $DOM_SLUG;
        $GLOBALS[$SITE]['DOM_DISPLAY'] = $DOM_DISPLAY;
        $GLOBALS[$SITE]['ROOM_SLUG'] = $ROOM_SLUG;
        $GLOBALS[$SITE]['ROOM_DISPLAY'] = $ROOM_DISPLAY;
        $GLOBALS['ROOM_FLAVOR'] = $ROOM_FLAVOR;

        define('MOD_SLUG', $MOD_SLUG);
        define('MOD_DISPLAY', $MOD_DISPLAY);
        define('DOM_SLUG', $DOM_SLUG);
        define('DOM_DISPLAY', $DOM_DISPLAY);
        define('ROOM_SLUG', $ROOM_SLUG);
        define('ROOM_DISPLAY', $ROOM_DISPLAY);
        define('ROOM_FLAVOR', $ROOM_FLAVOR);
}


function SKY_AUTO_FAILURE(){
  openSky("You are LOST");
  medHeading("There is room and keys here. You can't see any of them.");
  leaf("Are you forgetting something?");
}


function getSkyAUTH(string $SYSTEM_PATH) {
  $SITE = $GLOBALS['SITE'];
  
  if (!is_dir($SYSTEM_PATH)) {
    SKY_AUTO_FAILURE();
  require resolveShell();
  exit;
  } 
}
