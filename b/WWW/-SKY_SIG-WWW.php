<?php // new naming migration //

define('WORLD_ID', 'WWW');
define('WORLD_TAG', "MIRA ONLINE");
define('BLOCK_ID', 'WWW');
define('BLOCK_URI', 'WWW');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>