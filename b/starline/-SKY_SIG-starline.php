<?php
// Cosmology: SYS starline. Compat aliases WORLD_/BLOCK_ until rename pass.
define('SYS_ID', 'starline');
define('SYS_TAG', 'THE BEYOND BEGINS HERE');
define('WORLD_ID', SYS_ID);
define('WORLD_TAG', SYS_TAG);
define('BLOCK_ID', SYS_ID);
define('BLOCK_URI', SYS_ID);

global $SITE;
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = SYS_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = SYS_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = SYS_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>