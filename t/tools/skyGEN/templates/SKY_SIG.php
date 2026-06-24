<?php // new naming migration //

define('WORLD_ID', '{{WORLD_SLUG}}');
define('WORLD_TAG', "{{WORLD_DISPLAY}}");
define('BLOCK_ID', '{{WORLD_SLUG}}');
define('BLOCK_URI', '{{URI}}');

global $SITE;
// in-phase-out
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = WORLD_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = WORLD_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>