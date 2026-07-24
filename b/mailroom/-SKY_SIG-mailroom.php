<?php
// Cosmology: SYS mailroom — Chester network Master Mailroom (Charlie). Not a terminal.
define('SYS_ID', 'mailroom');
define('SYS_TAG', 'MASTER MAILROOM');
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
