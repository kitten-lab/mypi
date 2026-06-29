<?php // new naming migration //

define('WORLD_ID', 'book');
define('WORLD_TAG', "book");
define('BLOCK_ID', 'book');
define('BLOCK_URI', 'book');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>