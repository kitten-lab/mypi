<?php // new naming migration //

define('BLOCK_ID', 'SKYLINE');
define('BLOCK_TAG', "SKYLINE");
define('BLOCK_URI', 'SKYLINE');

// in-phase-out
global $SITE;

$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = BLOCK_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>