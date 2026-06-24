<?php // new naming migration //

define('WORLD_ID', 'nimLOGGER');
define('WORLD_TAG', "NIM JACK LOGGER SYSTEM");
define('BLOCK_ID', 'nimLOGGER');
define('BLOCK_URI', 'nimLOGGER');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>