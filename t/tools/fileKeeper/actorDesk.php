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

if ($action === 'attach') {
    $stem = trim((string) ($_POST['fk_stem'] ?? ''));
    $cUid = trim((string) ($_POST['fk_c_uid'] ?? ''));
    $role = trim((string) ($_POST['fk_media_role'] ?? 'attach'));
    if ($role === '') {
        $role = 'attach';
    }
    if ($cUid === '' && $stem !== '') {
        // attach to current head of stem
        $heads = mypi_ledger_file_heads([
            'sys' => $place['sys'],
            'dom' => $place['dom'],
            'room' => $place['room'],
            'limit' => 200,
        ]);
        foreach ($heads as $h) {
            $hm = json_decode((string) ($h['meta_json'] ?? '{}'), true) ?: [];
            $hs = (string) ($hm['stem_c_uid'] ?? $h['c_uid']);
            if ($hs === $stem) {
                $cUid = $h['c_uid'];
                break;
            }
        }
    }
    if ($cUid === '') {
        $GLOBALS['FILEKEEPER_ERROR'] = 'no file to attach to';
        return;
    }
    if (empty($_FILES['fk_image']['tmp_name']) || !is_uploaded_file($_FILES['fk_image']['tmp_name'])) {
        $GLOBALS['FILEKEEPER_ERROR'] = 'no image uploaded';
        return;
    }
    $stored = mypi_media_store(
        $_FILES['fk_image']['tmp_name'],
        (string) ($_FILES['fk_image']['name'] ?? 'upload.png'),
        ['c_uid' => $cUid, 'stem_c_uid' => $stem, 'role' => $role]
    );
    if (empty($stored['ok'])) {
        $GLOBALS['FILEKEEPER_ERROR'] = $stored['error'] ?? 'store failed';
        return;
    }
    $att = mypi_media_attach_crate($cUid, $stored, $role);
    if (empty($att['ok'])) {
        $GLOBALS['FILEKEEPER_ERROR'] = $att['error'] ?? 'attach failed';
        return;
    }
    // append markdown ref into body if body is the IMG SUPPORT placeholder or empty
    $row = mypi_ledger_get($cUid);
    if ($row) {
        $body = (string) ($row['body'] ?? '');
        $md = "\n\n![" . ($stored['name'] ?? 'image') . "](media:" . $stored['asset_id'] . ")\n";
        if (trim($body) === '' || stripos($body, 'INSTALL IMG SUPPORT') !== false || stripos($body, 'ERROR, PLEASE INSTALL IMG') !== false) {
            // keep the prophecy line, then unlock
            if (stripos($body, 'INSTALL IMG SUPPORT') !== false || stripos($body, 'ERROR, PLEASE INSTALL IMG') !== false) {
                $body = trim($body) . "\n\n<!-- IMG SUPPORT ONLINE -->" . $md;
            } else {
                $body = $md;
            }
            mypi_ledger_pdo()->prepare('UPDATE crates SET body = ?, updated_at = ? WHERE c_uid = ?')
                ->execute([$body, time(), $cUid]);
        }
    }
    $q = ['stem' => $stem !== '' ? $stem : $cUid, 'fk_ok' => 'img'];
    header('Location: ' . $path . '?' . http_build_query($q));
    exit;
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
