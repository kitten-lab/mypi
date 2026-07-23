<?php
/**
 * cmsBASIC · save headline / article (mythleak newsroom)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

$action = (string) ($_POST['cms_action'] ?? '');
if ($action !== 'save') {
    return;
}

$place = function_exists('mypi_ledger_place_from_sky')
    ? mypi_ledger_place_from_sky()
    : ['sys' => 'mythleak', 'dom' => 'news', 'room' => 'headlines'];

$sys = $place['sys'] !== '' ? $place['sys'] : 'mythleak';
$dom = $place['dom'] !== '' ? $place['dom'] : 'news';
$room = 'headlines';

$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : 'mouse';
$byline = trim((string) ($_POST['cms_byline'] ?? ''));
if ($byline === '') {
    $byline = defined('MOD_DISPLAY') ? (string) MOD_DISPLAY : '-/mouse';
}
$tz = trim((string) ($_POST['cms_tz'] ?? ''));
$title = trim((string) ($_POST['cms_title'] ?? ''));
$dek = trim((string) ($_POST['cms_dek'] ?? ''));
$body = (string) ($_POST['cms_body'] ?? '');
$tags = trim((string) ($_POST['cms_tags'] ?? ''));
$cUid = trim((string) ($_POST['cms_c_uid'] ?? ''));
$eventRaw = trim((string) ($_POST['cms_event'] ?? ''));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/mythleak/news/headlines';

$go = static function (string $to) {
    header('Location: ' . $to);
    exit;
};

if ($title === '') {
    $GLOBALS['CMS_ERROR'] = 'headline required';
    return;
}

// event date = when the story *happened* (log moment), not ingest
$eventUnix = null;
if ($eventRaw !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventRaw)) {
    $tzUse = $tz !== '' ? $tz : 'America/New_York';
    try {
        $dt = new DateTimeImmutable($eventRaw . ' 12:00:00', new DateTimeZone($tzUse));
        $eventUnix = $dt->getTimestamp();
    } catch (Throwable $e) {
        $eventUnix = strtotime($eventRaw . ' 12:00:00');
    }
}

// if dek blank, peel first body line when it looks like a subhead
if ($dek === '') {
    $lines = preg_split('/\R/', $body) ?: [];
    $first = trim((string) ($lines[0] ?? ''));
    if ($first !== '' && strlen($first) <= 200 && ($first === strtoupper($first) || strlen($first) < 120)) {
        $dek = $first;
        $body = trim(implode("\n", array_slice($lines, 1)));
    }
} else {
    $lines = preg_split('/\R/', $body) ?: [];
    $first = trim((string) ($lines[0] ?? ''));
    if ($first !== '' && strcasecmp($first, $dek) === 0) {
        $body = trim(implode("\n", array_slice($lines, 1)));
    }
}

$meta = [
    'dek' => $dek,
    'byline' => $byline,
    'public_domain' => 'mythleak.com',
    'host' => 'imported.to',
];
if ($eventRaw !== '') {
    $meta['event_raw'] = $eventRaw;
}

if ($cUid !== '') {
    $row = mypi_ledger_get($cUid);
    if (!$row || ($row['kind'] ?? '') !== 'headline') {
        $GLOBALS['CMS_ERROR'] = 'article not found';
        return;
    }
    $old = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
    $meta = array_merge($old, $meta);
    $sql = 'UPDATE crates SET topic=?, body=?, meta_json=?, tags_raw=?, tags_json=?, updated_at=?, actor=?';
    $args = [
        $title,
        $body,
        json_encode($meta),
        $tags,
        json_encode(mypi_ledger_parse_tags($tags, $sys, $dom, $room, '')),
        time(),
        $byline,
    ];
    if ($eventUnix !== null) {
        $sql .= ', event_unix=?';
        $args[] = $eventUnix;
    }
    $sql .= ' WHERE c_uid=?';
    $args[] = $cUid;
    mypi_ledger_pdo()->prepare($sql)->execute($args);
    $pdo = mypi_ledger_pdo();
    $pdo->prepare('DELETE FROM tag_map WHERE c_uid=?')->execute([$cUid]);
    $ins = $pdo->prepare('INSERT OR IGNORE INTO tag_map(c_uid, tag) VALUES(?,?)');
    foreach (mypi_ledger_parse_tags($tags, $sys, $dom, $room, '') as $t) {
        $ins->execute([$cUid, $t]);
    }
    $go('/mythleak/news/article?id=' . rawurlencode($cUid) . '&ok=updated');
}

$create = [
    'topic' => $title,
    'body' => $body,
    'kind' => 'headline',
    'scale' => 'leaf',
    'tool' => 'cmsBASIC',
    'tool_version' => 1,
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'mod' => '',
    'place_label' => 'THE JUICE LINE',
    'agent' => $agentSlug,
    'actor' => $byline,
    'timezone' => $tz,
    'tags_raw' => $tags !== '' ? $tags : 'mythleak,headline',
    'meta' => $meta,
];
if ($eventUnix !== null) {
    $create['event_unix'] = $eventUnix;
}

$r = mypi_ledger_create_post($create);

if (empty($r['ok'])) {
    $GLOBALS['CMS_ERROR'] = $r['error'] ?? 'save failed';
    return;
}

$go('/mythleak/news/article?id=' . rawurlencode((string) $r['c_uid']) . '&ok=filed');
