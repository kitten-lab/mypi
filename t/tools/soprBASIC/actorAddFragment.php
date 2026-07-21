<?php
/**
 * soprBASIC AddFragment → mypi ledger (kind=soper).
 */
require_once __DIR__ . '/-SIG-soprBASIC.php';
require_once __DIR__ . '/-CRATE-soprBASIC.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$result = soprBASIC_ledger_store();
$GLOBALS['SOPR_LAST'] = $result;
if (!empty($result['ok'])) {
    $GLOBALS['SOPR_CONFIRM'] = 'FRAGMENT STORED · ' . $result['c_uid']
        . ' · TPS ' . ($result['tps_uid'] ?? '')
        . (!empty($result['edges']) ? ' · Charlie edges:' . $result['edges'] : '');
} else {
    $GLOBALS['SOPR_CONFIRM'] = 'STORE FAILED · ' . ($result['error'] ?? 'unknown');
}
