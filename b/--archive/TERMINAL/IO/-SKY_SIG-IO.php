<?php // new naming migration //

define('BLOCK_ID', 'TERMINAL');
define('BLOCK_TAG', "TERMINAL.IO");
define('BLOCK_URI', 'TERMINAL/IO');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = BLOCK_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>