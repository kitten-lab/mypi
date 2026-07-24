<?php
/**
 * timberBay · Charlie mailroom tag actions
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['tb_action'] ?? '');
$cUid = trim((string) ($_POST['tb_c_uid'] ?? ''));
$frag = trim((string) ($_POST['tb_frag'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/mailroom/floor/sort';

$q = [
    'id' => $cUid,
    'queue' => (string) ($_POST['tb_queue'] ?? 'all'),
    'sort' => (string) ($_POST['tb_sort'] ?? 'ingest'),
    'kind' => (string) ($_POST['tb_kind'] ?? ''),
    'agent' => (string) ($_POST['tb_agent'] ?? ''),
    'place' => (string) ($_POST['tb_place'] ?? ''),
    'q' => (string) ($_POST['tb_q'] ?? ''),
];

$go = static function (array $q) use ($path) {
    $q = array_filter($q, static fn($v) => $v !== '' && $v !== null);
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
};

if ($cUid === '') {
    $GLOBALS['TBAY_ERROR'] = 'no timber selected';
    return;
}

$opts = ['actor' => 'charlie', 'tool' => 'timberBay'];

if ($action === 'append') {
    $r = mypi_ledger_append_charlie($cUid, $frag, $opts);
    if (empty($r['ok'])) {
        $GLOBALS['TBAY_ERROR'] = $r['error'] ?? 'append failed';
        return;
    }
    $q['ok'] = 'tagged';
    $go($q);
}

if ($action === 'set') {
    $r = mypi_ledger_set_charlie($cUid, $frag, $opts);
    if (empty($r['ok'])) {
        $GLOBALS['TBAY_ERROR'] = $r['error'] ?? 'set failed';
        return;
    }
    $q['ok'] = 'set';
    $go($q);
}

$GLOBALS['TBAY_ERROR'] = 'unknown action';
