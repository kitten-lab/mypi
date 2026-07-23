<?php
// Cosmology: SYS mythleak — onion of the gods · hosted on imported.to (lore)
define('SYS_ID', 'mythleak');
define('SYS_TAG', 'MYTHLEAK');
define('WORLD_ID', SYS_ID);
define('WORLD_TAG', 'MYTHLEAK — WE HAVE THE RECIEPTS');
define('BLOCK_ID', SYS_ID);
define('BLOCK_URI', SYS_ID);

global $SITE;
$GLOBALS['SITE'] = BLOCK_ID;
$GLOBALS[$SITE]['SYS'] = SYS_ID;
$GLOBALS[$SITE]['SYS_SLUG'] = SYS_ID;
$GLOBALS[$SITE]['SYS_DISPLAY'] = SYS_TAG;
$GLOBALS[$SITE]['URI'] = BLOCK_URI;
?>
