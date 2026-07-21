<?php
// SYS www — DEMO/WWW restored (archive rooms + asSys), not a remake shell
define('SYS_ID', 'www');
define('SYS_TAG', 'DEMO/WWW');
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
