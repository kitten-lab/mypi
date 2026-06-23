<?php // new naming migration //

define('BLOCK_ID', 'WWW');
define('BLOCK_TAG', "WWW(demo)");
define('BLOCK_URI', 'DEMO/WWW');

// in-phase-out
global $SITE;
$SITE = BLOCK_ID;

$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = BLOCK_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>