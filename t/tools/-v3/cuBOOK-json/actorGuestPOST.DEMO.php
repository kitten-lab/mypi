<?php
require_once $GLOBALS['INTERA']['SYSTEM'] . 'chestersCrates.php';
require_once __DIR__ . '/-SIG-cuBOOK.php';
require_once __DIR__ . '/-CRATE-cuBOOK.php';
require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';

$IS_IT = $GLOBALS['TOOL']['SHADOWENVO'] ?? false;
$sha_env = shadowENVO($IS_IT);
if ($IS_IT == true) {
    echo "<div class='sha_env'>shadow mode on</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prefer full chesters path when available; always ensure guestbook file writes
    if (function_exists('demoSHIPMENT')) {
        require_once ROUTE_TO_SYSTEMS . 'tpsMACHINE.php';
        demoSHIPMENT($sha_env, $event_time ?? time(), $unix ?? time());
        if (function_exists('demoTHREADS')) {
            demoTHREADS($sha_env, $tpstime ?? time());
        }
        if (function_exists('demoTPSReports')) {
            demoTPSReports($sha_env, $tpstime ?? null, $ms ?? null, $event_time ?? null, $syear ?? null);
        }
    } else {
        // Lean store — matches pageViewCUs chest shape
        cuBOOK_STORE(is_string($sha_env) ? $sha_env : '');
    }
}
