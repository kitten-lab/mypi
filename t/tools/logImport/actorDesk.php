<?php
/**
 * logImport · save WIP only (never writes glass shards).
 * Submit-to-ledger comes later.
 */
require_once __DIR__ . '/logImport_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}
$action = (string) ($_POST['logimport_action'] ?? '');
if ($action !== 'save_wip') {
    return;
}

$face = trim((string) ($_POST['face'] ?? ''));
if ($face === '' || !logimport_core_by_face($face)) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'unknown core';
    return;
}

$wip = [
    'yard_title' => trim((string) ($_POST['yard_title'] ?? '')),
    'notes' => (string) ($_POST['notes'] ?? ''),
    // encode / redact / segments later
    'encodes' => [],
    'redactions' => [],
    'segments' => [],
];

if (!logimport_wip_save($face, $wip)) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'could not write wip';
    return;
}

// soft redirect stay on face
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$q = 'face=' . rawurlencode(logimport_face_key($face)) . '&wip_ok=1';
header('Location: ' . $path . '?' . $q);
exit;
