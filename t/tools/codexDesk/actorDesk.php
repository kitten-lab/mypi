<?php
/**
 * codexDesk · system / world lore (RX · Oriel)
 * kind=codex_entry · place terminal/rx/codex
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['cx_action'] ?? '');
if ($action !== 'save' && $action !== 'delete') {
    return;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$go = static function (array $q) use ($path) {
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
};

$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : 'oriel';
$tz = trim((string) ($_POST['cx_tz'] ?? ''));

if ($action === 'delete') {
    $cUid = trim((string) ($_POST['cx_c_uid'] ?? ''));
    if ($cUid === '') {
        $GLOBALS['CODEX_ERROR'] = 'nothing to delete';
        return;
    }
    $row = mypi_ledger_get($cUid);
    if (!$row || ($row['kind'] ?? '') !== 'codex_entry') {
        $GLOBALS['CODEX_ERROR'] = 'entry not found';
        return;
    }
    // soft-delete if column exists
    try {
        mypi_ledger_pdo()->prepare(
            'UPDATE crates SET deleted_at=?, updated_at=? WHERE c_uid=?'
        )->execute([time(), time(), $cUid]);
    } catch (Throwable $e) {
        $GLOBALS['CODEX_ERROR'] = 'delete failed';
        return;
    }
    $go(['tab' => 'list', 'ok' => 'deleted']);
}

$title = trim((string) ($_POST['cx_title'] ?? ''));
$body = (string) ($_POST['cx_body'] ?? '');
$category = trim((string) ($_POST['cx_category'] ?? 'system'));
$kven = strtoupper(trim((string) ($_POST['cx_kven'] ?? '')));
$tags = trim((string) ($_POST['cx_tags'] ?? ''));
$cUid = trim((string) ($_POST['cx_c_uid'] ?? ''));

if ($title === '') {
    $GLOBALS['CODEX_ERROR'] = 'title required';
    return;
}

$allowed = ['system', 'person', 'place', 'event', 'tech', 'world', 'other'];
if (!in_array($category, $allowed, true)) {
    $category = 'system';
}

$meta = [
    'category' => $category,
    'kven' => $kven,
];

if ($cUid !== '') {
    $row = mypi_ledger_get($cUid);
    if (!$row || ($row['kind'] ?? '') !== 'codex_entry') {
        $GLOBALS['CODEX_ERROR'] = 'entry not found';
        return;
    }
    $old = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
    $meta = array_merge($old, $meta);
    $meta['updated_by'] = $agentSlug;
    $tagJson = json_encode(mypi_ledger_parse_tags($tags, 'terminal', 'rx', 'codex', ''));
    // crates has agent (not actor) — actor lives on crate_events / deleted_log
    mypi_ledger_pdo()->prepare(
        'UPDATE crates SET topic=?, body=?, meta_json=?, tags_raw=?, tags_json=?, updated_at=?, agent=? WHERE c_uid=?'
    )->execute([
        $title,
        $body,
        json_encode($meta),
        $tags,
        $tagJson,
        time(),
        $agentSlug,
        $cUid,
    ]);
    $pdo = mypi_ledger_pdo();
    $pdo->prepare('DELETE FROM tag_map WHERE c_uid=?')->execute([$cUid]);
    $ins = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
    foreach (mypi_ledger_parse_tags($tags, 'terminal', 'rx', 'codex', '') as $t) {
        $ins->execute([$cUid, $t]);
    }
    $go(['tab' => 'view', 'id' => $cUid, 'ok' => 'updated']);
}

$r = mypi_ledger_create_post([
    'topic' => $title,
    'body' => $body,
    'kind' => 'codex_entry',
    'scale' => 'leaf',
    'tool' => 'codexDesk',
    'tool_version' => 1,
    'sys' => 'terminal',
    'dom' => 'rx',
    'room' => 'codex',
    'mod' => '',
    'place_label' => 'Codex',
    'agent' => $agentSlug,
    'actor' => $agentSlug,
    'timezone' => $tz,
    'tags_raw' => $tags !== '' ? $tags : 'codex,lore',
    'meta' => $meta,
]);

if (empty($r['ok'])) {
    $GLOBALS['CODEX_ERROR'] = $r['error'] ?? 'save failed';
    return;
}

$go(['tab' => 'view', 'id' => $r['c_uid'], 'ok' => 'filed']);
