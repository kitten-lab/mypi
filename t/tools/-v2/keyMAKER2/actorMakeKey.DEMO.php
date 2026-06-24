<?php
require_once $GLOBALS['INTERA']['SYSTEM'] . 'chestersCrates.php'; // CHEST CRATING SYSTEM

require_once __DIR__ . '/-SIG-keyMAKER2.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-keyMAKER2.php'; // CRATE FILLER SETTINGS

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require __DIR__ . '/../tpsMACHINE.php';  // THE TPS MACHINE 

    $cUID = SKY_GET_cUID($event_time);
    $tUID = SKY_GET_tUID($event_time);

    // ============================================================================
    // OKAY LETS CATALOG AND CRATE THIS BIT OF STUFFS! 
    //=============================================================================

    demoSHIPMENT($sha_env, $tpstime, $unix, $timezone);
    demoTHREADS($sha_env, $tpstime);

    //=============================================================================
    // OH $@%! -- DON'T FORGET YOUR TPS REPORT
    // ============================================================================

    demoTPSReports($sha_env, $tpstime, $ms, $event_time, $syear);
}
