<?php
/**
 * inventOry · insert leaf / close day
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['invent_action'] ?? 'insert');
$place = mypi_ledger_place_from_sky();
// invent leaves always live under inventory room
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'io';
$room = 'inventory';
$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : (defined('MOD_DISPLAY') ? MOD_DISPLAY : 'user');
$tz = trim((string) ($_POST['inv_tz'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

if ($action === 'close' || $action === 'reopen') {
    $dayUid = trim((string) ($_POST['inv_day_uid'] ?? ''));
    $r = mypi_ledger_dailylog_set_closed($dayUid, $action === 'close');
    $day = trim((string) ($_POST['inv_day'] ?? ''));
    $q = ['day' => $day];
    if (!empty($r['ok'])) {
        $q['inv_ok'] = $action;
    } else {
        $q['inv_err'] = $r['error'] ?? 'close failed';
    }
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
}

if ($action === 'import_vault') {
    $dir = trim((string) ($_POST['inv_vault_dir'] ?? ''));
    $force = !empty($_POST['inv_force']);
    if ($dir === '') {
        $dir = "D:\\_Chester's Imports\\Terminal IO\\USERS\\SDK808\\Daily Inventory";
    }
    $r = mypi_ledger_dailylog_import_dir([
        'dir' => $dir,
        'force' => $force,
        'sys' => $sys,
        'dom' => $dom,
        'room' => $room,
        'agent' => $agentSlug,
        'actor' => $agentSlug,
        'timezone' => $tz,
    ]);
    // stash summary in session-ish query (keep short)
    $q = [
        'inv_import' => '1',
        'imp_n' => count($r['imported'] ?? []),
        'skip_n' => count($r['skipped'] ?? []),
        'err_n' => count($r['errors'] ?? []),
    ];
    if (!empty($r['imported'][0]) && preg_match('/^(\d{4}-\d{2}-\d{2})/', $r['imported'][0], $m)) {
        $q['day'] = $m[1];
    }
    // full report via flash file (tiny)
    $flash = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'inventOry_import_' . md5($agentSlug) . '.json';
    @file_put_contents($flash, json_encode($r, JSON_PRETTY_PRINT));
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
}

if ($action !== 'insert') {
    return;
}

$day = trim((string) ($_POST['inv_day'] ?? date('Y-m-d')));
$title = trim((string) ($_POST['inv_title'] ?? ''));
$body = (string) ($_POST['inv_body'] ?? '');
$section = trim((string) ($_POST['inv_section'] ?? 'INCOMING EVENTS'));
$context = trim((string) ($_POST['inv_context'] ?? ''));
$tags = trim((string) ($_POST['inv_tags'] ?? ''));
$reportTo = trim((string) ($_POST['inv_report_to'] ?? 'none'));
$eventRaw = trim((string) ($_POST['inv_event'] ?? ''));
$eventUnix = trim((string) ($_POST['inv_event_unix'] ?? ''));

if (function_exists('mypi_sanitize_datetime_text')) {
    $eventRaw = mypi_sanitize_datetime_text($eventRaw);
}

$in = [
    'day' => $day,
    'title' => $title,
    'body' => $body,
    'section' => $section,
    'context' => $context,
    'tags_raw' => $tags,
    'report_to' => $reportTo,
    'event_raw' => $eventRaw,
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'mod' => '',
    'place_label' => 'invent-0rium',
    'agent' => $agentSlug,
    'actor' => $agentSlug,
    'timezone' => $tz,
];
if ($eventUnix !== '' && preg_match('/^\d{9,13}$/', $eventUnix)) {
    $eu = (int) $eventUnix;
    if ($eu > 9999999999) {
        $eu = (int) floor($eu / 1000);
    }
    $in['event_unix'] = $eu;
}

$result = mypi_ledger_dailylog_insert($in);

if (!empty($result['ok'])) {
    $q = [
        'day' => $result['day'] ?? $day,
        'inv_ok' => '1',
        'e' => $result['c_uid'] ?? '',
    ];
    if (!empty($result['report_c_uid'])) {
        $q['rpt'] = $result['report_c_uid'];
        $q['whom'] = $reportTo;
    }
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
}

$GLOBALS['INVENTORY_ERROR'] = $result['error'] ?? 'insert failed';
