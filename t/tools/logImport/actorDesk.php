<?php
/**
 * logImport · WIP actions (never writes glass).
 * save_wip | split_after | unsplit_after | clear_splits
 */
require_once __DIR__ . '/logImport_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['logimport_action'] ?? '');
$face = trim((string) ($_POST['face'] ?? ''));
$core = $face !== '' ? logimport_core_by_face($face) : null;
if (!$core) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'unknown core';
    return;
}
$faceKey = logimport_face_key((string) $core['face_id']);

// need message count for split bounds
$lastSeq = 0;
$conv = logimport_load_conversation($core);
if ($conv) {
    $msgs = logimport_extract_messages($conv);
    if ($msgs) {
        $lastSeq = (int) $msgs[count($msgs) - 1]['seq'];
    }
}

$existing = logimport_wip_load($faceKey);
$wip = logimport_wip_merge_form($faceKey, $_POST, $existing);
$segments = logimport_segments_normalize($wip['segments'] ?? [], $lastSeq);
$status = 'wip_ok';

if ($action === 'save_wip') {
    $wip['segments'] = $segments;
    // keep empty segments as [] meaning "whole log"
    if (count($segments) === 1
        && (int) $segments[0]['from_seq'] === 0
        && (int) $segments[0]['to_seq'] === $lastSeq
        && trim((string) $segments[0]['title']) === ''
    ) {
        $wip['segments'] = [];
    }
} elseif ($action === 'split_after') {
    $after = (int) ($_POST['after_seq'] ?? -1);
    $wip['segments'] = logimport_segments_add_cut($segments, $after, $lastSeq);
    $status = 'split_ok';
} elseif ($action === 'unsplit_after') {
    $after = (int) ($_POST['after_seq'] ?? -1);
    $wip['segments'] = logimport_segments_remove_cut($segments, $after, $lastSeq);
    $status = 'unsplit_ok';
} elseif ($action === 'clear_splits') {
    $wip['segments'] = [];
    $status = 'clear_ok';
} else {
    return;
}

if (!logimport_wip_save($faceKey, $wip)) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'could not write wip';
    return;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$q = 'face=' . rawurlencode($faceKey) . '&' . $status . '=1';
header('Location: ' . $path . '?' . $q);
exit;
