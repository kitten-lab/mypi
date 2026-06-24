<?php // new naming migration //

define('BLOCK_ID', 'MYTHLEAK');
define('BLOCK_TAG', "MYTHLEAK");
define('BLOCK_URI', 'MYTHLEAK');

// in-phase-out
global $SITE;
$SITE = BLOCK_ID;

// $GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = BLOCK_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>