<?php
/**
 * shotDesk · save shot cards + storyboard attach (ICU)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['sd_action'] ?? '');
$place = mypi_ledger_place_from_sky();
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'icu';
$room = 'shots';
$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : (defined('MOD_DISPLAY') ? MOD_DISPLAY : 'user');
$tz = trim((string) ($_POST['sd_tz'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

$base = [
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'mod' => '',
    'place_label' => 'Shots',
    'agent' => $agentSlug,
    'actor' => $agentSlug,
    'timezone' => $tz,
];

$go = static function (array $q) use ($path) {
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
};

if ($action === 'save_shot') {
    $r = mypi_shot_save(array_merge($base, [
        'c_uid' => trim((string) ($_POST['sd_c_uid'] ?? '')),
        'title' => trim((string) ($_POST['sd_title'] ?? '')),
        'code' => trim((string) ($_POST['sd_code'] ?? '')),
        'slugline' => trim((string) ($_POST['sd_slugline'] ?? '')),
        'visual' => (string) ($_POST['sd_visual'] ?? ''),
        'action' => (string) ($_POST['sd_action_body'] ?? ''),
        'dialogue' => (string) ($_POST['sd_dialogue'] ?? ''),
        'transition' => (string) ($_POST['sd_transition'] ?? ''),
        'amusement' => (string) ($_POST['sd_amusement'] ?? ''),
        'tags_raw' => trim((string) ($_POST['sd_tags'] ?? '')),
    ]));
    if (!empty($r['ok'])) {
        $go(['tab' => 'shot', 'id' => $r['c_uid'], 'ok' => !empty($r['updated']) ? 'updated' : 'shot']);
    }
    $GLOBALS['SHOT_ERROR'] = $r['error'] ?? 'save shot failed';
    return;
}

if ($action === 'attach') {
    $cUid = trim((string) ($_POST['sd_c_uid'] ?? ''));
    $role = trim((string) ($_POST['sd_media_role'] ?? 'storyboard'));
    if ($cUid === '' || empty($_FILES['sd_image']['tmp_name'])) {
        $GLOBALS['SHOT_ERROR'] = 'attach needs shot + image';
        return;
    }
    $stored = mypi_media_store(
        $_FILES['sd_image']['tmp_name'],
        (string) ($_FILES['sd_image']['name'] ?? 'storyboard.png'),
        ['c_uid' => $cUid, 'role' => $role]
    );
    if (empty($stored['ok'])) {
        $GLOBALS['SHOT_ERROR'] = $stored['error'] ?? 'store failed';
        return;
    }
    mypi_media_attach_crate($cUid, $stored, $role);
    $row = mypi_ledger_get($cUid);
    if ($row) {
        $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
        $meta['storyboard_asset'] = $stored['asset_id'];
        mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json=?, updated_at=? WHERE c_uid=?')
            ->execute([json_encode($meta), time(), $cUid]);
    }
    $go(['tab' => 'shot', 'id' => $cUid, 'ok' => 'img']);
}
