<?php
/**
 * cuBOOK GuestPOST → mypi ledger (kind=guestcu).
 */
require_once __DIR__ . '/-SIG-cuBOOK.php';
require_once __DIR__ . '/-CRATE-cuBOOK.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$result = cuBOOK_ledger_store();
$GLOBALS['CUBOOK_LAST'] = $result;
if (!empty($result['ok'])) {
    $GLOBALS['CUBOOK_CONFIRM'] = 'GUEST STORED · ' . $result['c_uid']
        . ' · TPS ' . ($result['tps_uid'] ?? '');
} else {
    $GLOBALS['CUBOOK_CONFIRM'] = 'STORE FAILED · ' . ($result['error'] ?? 'unknown');
}
