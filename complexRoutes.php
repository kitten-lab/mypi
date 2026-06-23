<?php 
define('ROUTE_TO_DESTINATIONS', echoSONAR . '/b/');
define('ROUTE_TO_TOOLS', echoSONAR . '/t/tools/');
define('ROUTE_TO_CONFIGS', echoSONAR . '/k/configs/');
define('ROUTE_TO_SYSTEMS', echoSONAR . '/k/systems/');
define('ROUTE_TO_ROUTES', echoSONAR . '/Settings/Routes/');
define('ROUTE_TO_LOCALCONFIG', echoSONAR . 'c/' . BLOCK_URI . '/');
define('ROUTE_TO_SHELL', echoSONAR . 'a/' . BLOCK_URI . '/');

define('ROUTE_TO_SATORA', echoSONAR . 'd/_SATORA/tps_reports/');
define('ROUTE_TO_CHESTER', echoSONAR . 'd/_CHESTER/search_by_crate/');
define('ROUTE_TO_CHARLIE', echoSONAR . 'd/_CHARLIE/threads/');


define('ROUTE_TO_LOCALSTORE', echoSONAR . 'd/' . BLOCK_URI . "/");
define('ROUTE_TO_DEWEY_LOOKUP', echoSONAR . 'd/_DEWEY/lookup/');

// figure out why there is dupe of this lookup
define('ROUTE_TO_DEWEY_CATALOG_D', echoSONAR . 'd/_DEWEY/catalogs/');
define('ROUTE_TO_DEWEY_CATALOG_B', echoSONAR . 'b/DEWEY/catalogs/');



function resolveShell() {
    $SYS = BLOCK_ID;
    $prime = echoSONAR . "a/" . $SYS . "/asSys/shell.php";
    $kroot = echoSONAR . "a/_/__sys.shell.php";

    return file_exists($prime) ? $prime : $kroot;
    }
// ----------------------------------------------------------------

//LEGACY --- TO RETIRE
$SONAR = echoSONAR;
$SYS = BLOCK_ID;
$URI = BLOCK_URI;

$GLOBALS['ROUTE']['B']['URI'] = $SONAR . "b/" . $URI . '/';
$GLOBALS['ROUTE']['A'][$SYS] = $SONAR . "a/" . $SYS . '/';
$GLOBALS['ROUTE']['C'][$URI] = $SONAR . "c/" . $URI . '/';
$GLOBALS['ROUTE']['C'][$SYS] = $SONAR . "c/" . $SYS . '/';
$GLOBALS['ROUTE']['M']['URI'] = $SONAR . "m/doors/" . $URI . '/';

define('ROOM_ROUTE', $SONAR . "m/doors/" . $URI . '/');
?>