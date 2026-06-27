<?php // new naming migration //

define('WORLD_ID', 'backrooms');
define('WORLD_TAG', "The Back Rooms");
define('BLOCK_ID', 'backrooms');
define('BLOCK_URI', 'backrooms');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>