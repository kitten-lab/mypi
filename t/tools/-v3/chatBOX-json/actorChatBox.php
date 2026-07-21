<?php 
ini_set('display_errors', '0'); 
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED); 
?>

<?php
// REQUIRED INCUDES
require_once ROUTE_TO_SYSTEMS . 'chestersCrates.php'; // CHEST CRATING SYSTEM
require_once __DIR__ . '/-SIG-chatBOX.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-chatBOX.php'; // CRATE FILLER SETTINGS
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';

// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}

// FORM PROCESSING 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$chattag = POST_username . "*chat-to>" . ROOM_SLUG;

    define("POST_TAGS", POST_username . "*speaks-to>" . ROOM_SLUG);
    define("POST_UNIX", $_POST['POST__EVENT_UNIX']);
    define("POST_PV", $GLOBALS['PV'] ?? "");

    require_once ROUTE_TO_SYSTEMS . 'tpsMACHINE.php';  // THE TPS MACHINE 

    define("TPS_TPSTIME", $tpstime);
    define("TPS_EVENTTIME", $event_time);
    define("TPS_UNIX", $unix);
    define("TPS_TIMEZONE", $timezone);
    define("TPS_MS", $ms);
    define("TPS_EVENTCALC", $event_calc);
    define("TPS_SYEAR", $syear);

    SKY_GET_cUID();
    SKY_GET_tUID();

    // ============================================================================
    // OKAY LETS CATALOG AND CRATE THIS BIT OF STUFFS! 
    //=============================================================================
    chatSTORE();

    chestersCRATES();
    charliesTHREADS();
    catalogUNIX();

    //=============================================================================
    // OH $@%! -- DON'T FORGET YOUR TPS REPORT
    // ============================================================================

    tpsREPORTS();
}
