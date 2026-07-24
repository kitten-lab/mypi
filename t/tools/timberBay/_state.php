<?php
/**
 * Shared request state for timberBay Rail + Desk (include once).
 */
if (!empty($GLOBALS['TBAY_STATE_READY'])) {
    return;
}
$GLOBALS['TBAY_STATE_READY'] = true;

require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$queue = isset($_GET['queue']) ? (string) $_GET['queue'] : 'all';
if (!in_array($queue, ['all', 'bare', 'terms', 'wired'], true)) {
    $queue = 'all';
}
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'ingest';
$kind = isset($_GET['kind']) ? trim((string) $_GET['kind']) : '';
$agent = isset($_GET['agent']) ? trim((string) $_GET['agent']) : '';
$place = isset($_GET['place']) ? trim((string) $_GET['place']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';
$err = $GLOBALS['TBAY_ERROR'] ?? null;

$timbers = mypi_ledger_list_timbers([
    'queue' => $queue,
    'sort' => $sort,
    'kind' => $kind,
    'agent' => $agent,
    'place' => $place,
    'q' => $q,
    'limit' => 120,
]);

$focus = null;
$nTags = 0;
$nEdges = 0;
$edges = [];
if ($id !== '') {
    $focus = mypi_ledger_get($id);
    if ($focus && (empty($focus['deleted_at']) || (int) $focus['deleted_at'] === 0)) {
        foreach ($timbers as $t) {
            if (($t['c_uid'] ?? '') === $id) {
                $nTags = (int) ($t['n_tags'] ?? 0);
                $nEdges = (int) ($t['n_edges'] ?? 0);
                break;
            }
        }
        if ($nTags === 0 && $nEdges === 0) {
            $pdo = mypi_ledger_pdo();
            $st = $pdo->prepare('SELECT COUNT(*) FROM tag_map WHERE c_uid=?');
            $st->execute([$id]);
            $nTags = (int) $st->fetchColumn();
            $st = $pdo->prepare('SELECT COUNT(*) FROM thread_edges WHERE c_uid=?');
            $st->execute([$id]);
            $nEdges = (int) $st->fetchColumn();
        }
        $st = mypi_ledger_pdo()->prepare(
            'SELECT from_term, rel, to_term FROM thread_edges WHERE c_uid=? ORDER BY id ASC LIMIT 40'
        );
        $st->execute([$id]);
        $edges = $st->fetchAll() ?: [];
    } else {
        $focus = null;
        $id = '';
    }
}

$tagsRaw = $focus ? (string) ($focus['tags_raw'] ?? '') : '';
$parsed = $focus ? mypi_ledger_parse_charlie($tagsRaw) : ['tags' => [], 'edges' => []];
$userTags = [];
foreach ($parsed['tags'] as $t) {
    $t = (string) $t;
    if ($t === '' || str_contains($t, '*') || str_contains($t, '>')
        || str_starts_with($t, 'path:') || str_starts_with($t, '@')
        || str_starts_with($t, 'sys:') || str_starts_with($t, 'dom:')
        || str_starts_with($t, 'mod:')) {
        continue;
    }
    $userTags[] = $t;
}
$userTags = array_values(array_unique($userTags));

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};

$qs = static function (array $extra = []) use ($queue, $sort, $kind, $agent, $place, $q, $id) {
    $base = array_merge([
        'queue' => $queue,
        'sort' => $sort,
        'kind' => $kind,
        'agent' => $agent,
        'place' => $place,
        'q' => $q,
        'id' => $id,
    ], $extra);
    return http_build_query(array_filter($base, static fn($v) => $v !== '' && $v !== null));
};

$stateBadge = static function (int $nt, int $ne): array {
    if ($ne > 0) {
        return ['wired', 'wired'];
    }
    if ($nt > 0) {
        return ['terms', 'terms'];
    }
    return ['bare', 'bare'];
};

// export into globals for Rail/Desk scopes if needed
$GLOBALS['TBAY'] = compact(
    'queue', 'sort', 'kind', 'agent', 'place', 'q', 'id', 'ok', 'err',
    'timbers', 'focus', 'nTags', 'nEdges', 'edges', 'userTags', 'parsed',
    'self', 'h', 'qs', 'stateBadge'
);
