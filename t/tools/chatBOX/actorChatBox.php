<?php
/**
 * chatBOX ChatBox → ledger line in a chat session.
 */
require_once __DIR__ . '/-SIG-chatBOX.php';
require_once __DIR__ . '/-CRATE-chatBOX.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$result = chatBOX_ledger_store();
$GLOBALS['CHATBOX_LAST'] = $result;
if (!empty($result['ok'])) {
    $GLOBALS['CHATBOX_CONFIRM'] = 'SAID · ' . $result['c_uid']
        . ' · session ' . ($result['session'] ?? 'live')
        . ' · TPS ' . ($result['tps_uid'] ?? '');
} else {
    $GLOBALS['CHATBOX_CONFIRM'] = 'CHAT FAILED · ' . ($result['error'] ?? 'unknown');
}
