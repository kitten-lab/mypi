<?php
/**
 * fileKeeper · Save / mkdir
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['filekeeper_action'] ?? '');
$place = mypi_ledger_place_from_sky();
$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : (defined('MOD_DISPLAY') ? MOD_DISPLAY : 'user');
$tz = trim((string) ($_POST['fk_tz'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

if ($action === 'mkdir') {
    $folder = trim((string) ($_POST['fk_mkdir'] ?? ''));
    $result = mypi_ledger_file_mkdir([
        'folder' => $folder,
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'mod' => $place['mod'],
        'place_label' => $place['place_label'],
        'agent' => $agentSlug,
        'timezone' => $tz,
    ]);
    if (!empty($result['ok'])) {
        $q = http_build_query(['new' => '1', 'folder' => $result['folder'], 'fk_mkdir_ok' => '1']);
        header('Location: ' . $path . '?' . $q);
        exit;
    }
    $GLOBALS['FILEKEEPER_ERROR'] = $result['error'] ?? 'mkdir failed';
    return;
}

if ($action !== 'save') {
    return;
}

$title = trim((string) ($_POST['fk_title'] ?? ''));
$body = (string) ($_POST['fk_body'] ?? '');
$tags = trim((string) ($_POST['fk_tags'] ?? ''));
$parent = trim((string) ($_POST['fk_parent'] ?? ''));
$stem = trim((string) ($_POST['fk_stem'] ?? ''));
$eventRaw = trim((string) ($_POST['fk_event'] ?? ''));
$eventResolved = trim((string) ($_POST['fk_event_unix'] ?? ''));
// folder: select or new-name field
$folder = trim((string) ($_POST['fk_folder'] ?? ''));
if ($folder === '__new__') {
    $folder = trim((string) ($_POST['fk_folder_new'] ?? ''));
}
if (function_exists('mypi_sanitize_datetime_text')) {
    $eventRaw = mypi_sanitize_datetime_text($eventRaw);
}

$eventUnix = null;
if ($eventResolved !== '' && preg_match('/^\d{9,13}$/', $eventResolved)) {
    $eventUnix = (int) $eventResolved;
    if ($eventUnix > 9999999999) {
        $eventUnix = (int) floor($eventUnix / 1000);
    }
}
if (($eventUnix === null || $eventUnix <= 0) && $eventRaw !== '') {
    $eventUnix = mypi_parse_event_time($eventRaw, $tz);
}
if ($eventUnix === null || $eventUnix <= 0) {
    $eventUnix = time();
}

$result = mypi_ledger_file_save([
    'title' => $title,
    'body' => $body,
    'tags_raw' => $tags,
    'parent_c_uid' => $parent,
    'stem_c_uid' => $stem,
    'folder' => $folder,
    'sys' => $place['sys'],
    'dom' => $place['dom'],
    'room' => $place['room'],
    'mod' => $place['mod'],
    'place_label' => $place['place_label'],
    'agent' => $agentSlug,
    'actor' => $agentSlug,
    'timezone' => $tz,
    'event_unix' => $eventUnix,
    'event_raw' => $eventRaw,
    'meta' => [
        'event_raw' => $eventRaw,
        'event_unix_resolved' => $eventUnix,
    ],
]);

if (!empty($result['ok'])) {
    $q = http_build_query([
        'stem' => $result['stem_c_uid'],
        'fk_ok' => '1',
        'rev' => $result['rev'] ?? '',
        'ev' => $eventUnix,
    ]);
    header('Location: ' . $path . '?' . $q);
    exit;
}

$GLOBALS['FILEKEEPER_ERROR'] = $result['error'] ?? 'save failed';
