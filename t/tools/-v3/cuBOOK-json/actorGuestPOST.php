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
    require_once ROUTE_TO_SYSTEMS . 'tpsMACHINE.php';

    if (function_exists('chestersCRATES')) {
        try {
            chestersCRATES($sha_env, $tpstime ?? time(), $unix ?? time(), $timezone ?? 'America/New_York');
            if (function_exists('charliesTHREADS')) {
                charliesTHREADS($sha_env, $tpstime ?? time());
            }
            if (function_exists('catalogUNIX')) {
                catalogUNIX($sha_env, $tpstime ?? time());
            }
            if (function_exists('tpsREPORTS')) {
                tpsREPORTS($sha_env, $tpstime ?? null, $ms ?? null, $event_time ?? null, $syear ?? null);
            }
        } catch (Throwable $e) {
            // fall through to lean store
            error_log('cuBOOK full crate path failed: ' . $e->getMessage());
            cuBOOK_STORE(is_string($sha_env) ? $sha_env : '');
        }
    } else {
        cuBOOK_STORE(is_string($sha_env) ? $sha_env : '');
    }

    // Always also write guestcu view chest (pageViewCUs)
    if (function_exists('cuBOOK_STORE')) {
        cuBOOK_STORE(is_string($sha_env) ? $sha_env : '');
    }
}
