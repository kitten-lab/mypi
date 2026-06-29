<?php // new naming migration //

define('WORLD_ID', 'interra');
define('WORLD_TAG', "interra");
define('BLOCK_ID', 'interra');
define('BLOCK_URI', 'interra');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>