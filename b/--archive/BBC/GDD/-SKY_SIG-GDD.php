<?php // new naming migration //

define('BLOCK_ID', 'BBC');
define('BLOCK_TAG', "GAME DESIGN DISTRICT");
define('BLOCK_URI', 'BBC/GDD');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = BLOCK_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>