<?php
/**
 * venDesk · save / delete VEN rows (z/ven_registry only)
 */
require_once __DIR__ . '/venDesk_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['vd_action'] ?? '');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$go = static function (array $q) use ($path) {
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
};

$reg = vendesk_load();

if ($action === 'save') {
    $id = trim((string) ($_POST['vd_id'] ?? ''));
    $kvenIn = trim((string) ($_POST['vd_kven'] ?? ''));
    $label = trim((string) ($_POST['vd_label'] ?? ''));
    $alts = vendesk_split_list((string) ($_POST['vd_alts'] ?? ''));
    $matches = vendesk_split_list((string) ($_POST['vd_matches'] ?? ''));
    $notes = (string) ($_POST['vd_notes'] ?? '');
    $type = trim((string) ($_POST['vd_type'] ?? 'person'));

    if ($kvenIn === '') {
        $kvenIn = vendesk_mint_kven($reg, $label !== '' ? $label : ($alts[0] ?? 'VEN'));
    }
    $kven = vendesk_normalize_kven($kvenIn);
    if (!vendesk_valid_kven($kven)) {
        $GLOBALS['VEN_ERROR'] = 'KVEN must be ABC-123 (3 letters, 3 digits)';
        return;
    }

    // unique kven
    foreach ($reg['entries'] as $e) {
        if (($e['kven'] ?? '') === $kven && ($e['id'] ?? '') !== $id) {
            $GLOBALS['VEN_ERROR'] = 'KVEN already assigned: ' . $kven;
            return;
        }
    }

    $now = time();
    $found = false;
    $isModify = false;
    foreach ($reg['entries'] as $i => $e) {
        if ($id !== '' && ($e['id'] ?? '') === $id) {
            $reg['entries'][$i] = vendesk_normalize_entry([
                'id' => $id,
                'kven' => $kven,
                'label' => $label,
                'alts' => $alts,
                'matches' => $matches,
                'notes' => $notes,
                'type' => $type,
                'created' => (int) ($e['created'] ?? $now),
                'updated' => $now,
            ]);
            $found = true;
            $isModify = true;
            $id = $reg['entries'][$i]['id'];
            break;
        }
    }
    if (!$found) {
        $entry = vendesk_normalize_entry([
            'kven' => $kven,
            'label' => $label,
            'alts' => $alts,
            'matches' => $matches,
            'notes' => $notes,
            'type' => $type,
            'created' => $now,
            'updated' => $now,
        ]);
        $reg['entries'][] = $entry;
        $id = $entry['id'];
    }

    if (!vendesk_save($reg)) {
        $GLOBALS['VEN_ERROR'] = 'could not write registry (z/ven_registry)';
        return;
    }
    // Big ledger awareness (public code + label only)
    // Desk direct: ADDED (new) vs MODIFY (edit). SHIP is logImport →VEN push.
    $ship = vendesk_ledger_ship(
        $kven,
        $label,
        'venDesk',
        $isModify ? 'modify' : 'add'
    );
    $q = ['tab' => 'view', 'id' => $id, 'ok' => 'saved'];
    if (!empty($ship['ok']) && !empty($ship['c_uid'])) {
        $q['c_uid'] = (string) $ship['c_uid'];
    }
    $go($q);
}

if ($action === 'delete') {
    $id = trim((string) ($_POST['vd_id'] ?? ''));
    $reg['entries'] = array_values(array_filter(
        $reg['entries'],
        static fn($e) => ($e['id'] ?? '') !== $id
    ));
    if (!vendesk_save($reg)) {
        $GLOBALS['VEN_ERROR'] = 'could not write registry';
        return;
    }
    $go(['tab' => 'list', 'ok' => 'deleted']);
}
