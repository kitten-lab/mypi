<?php // new naming migration //

define('WORLD_ID', 'starline');
define('WORLD_TAG', "THE BEYOND BEGINS HERE");
define('BLOCK_ID', 'starline');
define('BLOCK_URI', 'starline');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>