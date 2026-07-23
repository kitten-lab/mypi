<?php
/**
 * dossierDesk · person / faction / membership / note
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['dd_action'] ?? '');
$place = mypi_ledger_place_from_sky();
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'ab';
$room = 'dossier';
$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : (defined('MOD_DISPLAY') ? MOD_DISPLAY : 'user');
$tz = trim((string) ($_POST['dd_tz'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

$base = [
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'mod' => '',
    'place_label' => 'Dossier desk',
    'agent' => $agentSlug,
    'actor' => $agentSlug,
    'timezone' => $tz,
];

$go = static function (array $q) use ($path) {
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
};

if ($action === 'save_person') {
    $r = mypi_dossier_save_person(array_merge($base, [
        'c_uid' => trim((string) ($_POST['dd_c_uid'] ?? '')),
        'name' => trim((string) ($_POST['dd_name'] ?? '')),
        'body' => (string) ($_POST['dd_body'] ?? ''),
        'akas' => (string) ($_POST['dd_akas'] ?? ''),
        'status' => (string) ($_POST['dd_status'] ?? 'unsure'),
        'tags_raw' => trim((string) ($_POST['dd_tags'] ?? '')),
    ]));
    if (!empty($r['ok'])) {
        $go(['tab' => 'person', 'id' => $r['c_uid'], 'ok' => 'person']);
    }
    $GLOBALS['DOSSIER_ERROR'] = $r['error'] ?? 'save person failed';
    return;
}

if ($action === 'save_faction') {
    $r = mypi_dossier_save_faction(array_merge($base, [
        'c_uid' => trim((string) ($_POST['dd_c_uid'] ?? '')),
        'name' => trim((string) ($_POST['dd_name'] ?? '')),
        'body' => (string) ($_POST['dd_body'] ?? ''),
        'status' => (string) ($_POST['dd_status'] ?? 'unsure'),
        'tags_raw' => trim((string) ($_POST['dd_tags'] ?? '')),
    ]));
    if (!empty($r['ok'])) {
        $go(['tab' => 'faction', 'id' => $r['c_uid'], 'ok' => 'faction']);
    }
    $GLOBALS['DOSSIER_ERROR'] = $r['error'] ?? 'save faction failed';
    return;
}

if ($action === 'save_membership') {
    $r = mypi_dossier_save_membership(array_merge($base, [
        'person_c_uid' => trim((string) ($_POST['dd_person'] ?? '')),
        'faction_c_uid' => trim((string) ($_POST['dd_faction'] ?? '')),
        'status' => (string) ($_POST['dd_status'] ?? 'unsure'),
        'role' => trim((string) ($_POST['dd_role'] ?? '')),
        'is_leader' => !empty($_POST['dd_is_leader']),
    ]));
    if (!empty($r['ok'])) {
        $q = [
            'tab' => (string) ($_POST['dd_return_tab'] ?? 'person'),
            'id' => (string) ($_POST['dd_return_id'] ?? $_POST['dd_person'] ?? ''),
            'ok' => 'membership',
        ];
        if (!empty($r['leader_warn'])) {
            $q['leader_warn'] = (string) ($r['leader_count'] ?? 2);
        }
        $go($q);
    }
    $GLOBALS['DOSSIER_ERROR'] = $r['error'] ?? 'membership failed';
    return;
}

if ($action === 'add_note') {
    $r = mypi_dossier_add_note(array_merge($base, [
        'title' => trim((string) ($_POST['dd_title'] ?? '')),
        'body' => (string) ($_POST['dd_body'] ?? ''),
        'person_c_uid' => trim((string) ($_POST['dd_person'] ?? '')),
        'faction_c_uid' => trim((string) ($_POST['dd_faction'] ?? '')),
        'context' => trim((string) ($_POST['dd_context'] ?? '')),
        'confidence' => (string) ($_POST['dd_confidence'] ?? 'rumor'),
        'event_raw' => trim((string) ($_POST['dd_event'] ?? '')),
        'tags_raw' => trim((string) ($_POST['dd_tags'] ?? '')),
    ]));
    if (!empty($r['ok'])) {
        $go([
            'tab' => (string) ($_POST['dd_return_tab'] ?? 'person'),
            'id' => (string) ($_POST['dd_return_id'] ?? $_POST['dd_person'] ?? $_POST['dd_faction'] ?? ''),
            'ok' => 'note',
        ]);
    }
    $GLOBALS['DOSSIER_ERROR'] = $r['error'] ?? 'note failed';
    return;
}

if ($action === 'attach') {
    $cUid = trim((string) ($_POST['dd_c_uid'] ?? ''));
    $role = trim((string) ($_POST['dd_media_role'] ?? 'portrait'));
    if ($cUid === '' || empty($_FILES['dd_image']['tmp_name'])) {
        $GLOBALS['DOSSIER_ERROR'] = 'attach needs subject + image';
        return;
    }
    $stored = mypi_media_store(
        $_FILES['dd_image']['tmp_name'],
        (string) ($_FILES['dd_image']['name'] ?? 'portrait.png'),
        ['c_uid' => $cUid, 'role' => $role]
    );
    if (empty($stored['ok'])) {
        $GLOBALS['DOSSIER_ERROR'] = $stored['error'] ?? 'store failed';
        return;
    }
    mypi_media_attach_crate($cUid, $stored, $role);
    $row = mypi_ledger_get($cUid);
    if ($row) {
        $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
        if ($role === 'portrait' || ($row['kind'] ?? '') === 'dossier_person') {
            $meta['portrait_asset'] = $stored['asset_id'];
        }
        if ($role === 'sigil' || ($row['kind'] ?? '') === 'dossier_faction') {
            $meta['sigil_asset'] = $stored['asset_id'];
        }
        mypi_ledger_pdo()->prepare('UPDATE crates SET meta_json=?, updated_at=? WHERE c_uid=?')
            ->execute([json_encode($meta), time(), $cUid]);
    }
    $kind = $row['kind'] ?? '';
    $tab = $kind === 'dossier_faction' ? 'faction' : 'person';
    $go(['tab' => $tab, 'id' => $cUid, 'ok' => 'img']);
}
