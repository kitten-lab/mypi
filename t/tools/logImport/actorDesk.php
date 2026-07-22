<?php
/**
 * logImport · WIP actions (never writes glass).
 * save_wip | split_after | unsplit_after | clear_splits
 *
 * All actions come from one form so segment titles ride along on every cut.
 */
require_once __DIR__ . '/logImport_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$face = trim((string) ($_POST['face'] ?? ''));
$core = $face !== '' ? logimport_core_by_face($face) : null;
if (!$core) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'unknown core';
    return;
}
$faceKey = logimport_face_key((string) $core['face_id']);

// which button?
$action = (string) ($_POST['logimport_action'] ?? 'save_wip');
$afterSeq = null;
if (isset($_POST['split_after']) && $_POST['split_after'] !== '') {
    $action = 'split_after';
    $afterSeq = (int) $_POST['split_after'];
} elseif (isset($_POST['unsplit_after']) && $_POST['unsplit_after'] !== '') {
    $action = 'unsplit_after';
    $afterSeq = (int) $_POST['unsplit_after'];
} elseif (isset($_POST['clear_splits'])) {
    $action = 'clear_splits';
}

$lastSeq = 0;
$conv = logimport_load_conversation($core);
if ($conv) {
    $msgs = logimport_extract_messages($conv);
    if ($msgs) {
        $lastSeq = (int) $msgs[count($msgs) - 1]['seq'];
    }
}

$existing = logimport_wip_load($faceKey);
// Merge titles/notes from the same form POST (critical: titles before re-cut)
$wip = logimport_wip_merge_form($faceKey, $_POST, $existing);
// Prefer segments rebuilt from posted titles + existing cuts, not stale file alone
$segments = logimport_segments_from_post_and_wip($_POST, $wip, $lastSeq);

$status = 'wip_ok';
$anchorSeq = null;

if ($action === 'save_wip') {
    $wip['segments'] = logimport_segments_collapse_trivial($segments, $lastSeq);
} elseif ($action === 'split_after' && $afterSeq !== null) {
    $wip['segments'] = logimport_segments_add_cut($segments, $afterSeq, $lastSeq);
    $status = 'split_ok';
    $anchorSeq = $afterSeq;
} elseif ($action === 'unsplit_after' && $afterSeq !== null) {
    $wip['segments'] = logimport_segments_remove_cut($segments, $afterSeq, $lastSeq);
    $status = 'unsplit_ok';
    $anchorSeq = $afterSeq;
} elseif ($action === 'clear_splits') {
    $wip['segments'] = [];
    $status = 'clear_ok';
} else {
    $wip['segments'] = logimport_segments_collapse_trivial($segments, $lastSeq);
}

// Always keep yard_title / notes from merge
if (!logimport_wip_save($faceKey, $wip)) {
    $GLOBALS['LOGIMPORT_ERROR'] = 'could not write wip';
    return;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$q = 'face=' . rawurlencode($faceKey) . '&' . $status . '=1';
$frag = $anchorSeq !== null ? '#li-msg-' . (int) $anchorSeq : '';
header('Location: ' . $path . '?' . $q . $frag);
exit;
