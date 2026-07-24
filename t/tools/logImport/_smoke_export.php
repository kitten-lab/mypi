<?php
/**
 * One-shot smoke: seal export path + transform text type.
 * Run: php _smoke_export.php
 * Deletes its own smoke file on success.
 */
if (!defined('echoSONAR')) {
    define('echoSONAR', str_replace('\\', '/', dirname(__DIR__, 3)));
}
require __DIR__ . '/logImport_lib.php';

echo "echoSONAR=" . echoSONAR . PHP_EOL;
echo "submit=" . (function_exists('logimport_export_submit') ? 'y' : 'n') . PHP_EOL;
echo "transform=" . (function_exists('logimport_transform_text') ? 'y' : 'n') . PHP_EOL;

$tx = logimport_transform_text('hello secret world', 1, [
    'redactions' => [['kind' => 'phrase', 'original' => 'secret']],
    'encodes' => [],
], true, false);
if (!is_array($tx) || !is_string($tx['text'] ?? null)) {
    fwrite(STDERR, "FAIL: transform shape\n");
    exit(1);
}
echo "transform_ok text=" . $tx['text'] . PHP_EOL;

$paths = logimport_paths();
echo "exports_dir=" . $paths['exports'] . PHP_EOL;

$fakeCore = [
    'conversation_id' => 'test',
    'title' => 'smoke export',
    'testament_tag' => '',
    'create_time' => null,
];
$fakeWip = [
    'yard_title' => 'smoke-export-check',
    'notes' => 'export path check',
    'segments' => [],
    'encodes' => [
        [
            'original' => 'SECRET_NAME',
            'also' => ['SecName'],
            'code' => 'XXX-001',
            'alias' => 'PublicAlias',
        ],
    ],
    'redactions' => [['kind' => 'phrase', 'original' => 'secret']],
    'apply_encode' => false,
    'apply_redact' => true,
];

$ex = logimport_export_submit('SMOKE_FACE_TEST', $fakeCore, $fakeWip);
if (empty($ex['ok']) || !is_file($ex['path'])) {
    fwrite(STDERR, 'FAIL: export ' . ($ex['error'] ?? 'unknown') . PHP_EOL);
    exit(1);
}

$raw = (string) file_get_contents($ex['path']);
$j = json_decode($raw, true);
$ok =
    is_array($j)
    && ($j['schema'] ?? '') === 'logimport.export.v2'
    && ($j['face_id'] ?? '') !== ''
    && !empty($j['glass_sealed'])
    && is_array($j['messages'] ?? null)
    && ($j['yard_title'] ?? '') === 'smoke-export-check'
    && is_array($j['encodes_public'] ?? null)
    && !array_key_exists('encodes', $j)
    && !array_key_exists('wip_snapshot', $j)
    && strpos($raw, 'SECRET_NAME') === false
    && strpos($raw, 'SecName') === false
    && strpos($raw, 'PublicAlias') !== false
    && strpos($raw, 'XXX-001') !== false;

foreach ($j['messages'] ?? [] as $i => $m) {
    if (!is_string($m['text'] ?? null)) {
        fwrite(STDERR, "FAIL: message text not string at $i\n");
        exit(1);
    }
}

$pub = $j['encodes_public'][0] ?? [];
if (($pub['code'] ?? '') !== 'XXX-001' || ($pub['alias'] ?? '') !== 'PublicAlias') {
    fwrite(STDERR, "FAIL: encodes_public shape\n");
    exit(1);
}
if (isset($pub['original']) || isset($pub['also'])) {
    fwrite(STDERR, "FAIL: private encode fields leaked into encodes_public\n");
    exit(1);
}

echo "schema=" . ($j['schema'] ?? '') . PHP_EOL;
echo "face_id=" . ($j['face_id'] ?? '') . PHP_EOL;
echo "glass_sealed=" . var_export($j['glass_sealed'] ?? null, true) . PHP_EOL;
echo "encodes_public=" . json_encode($j['encodes_public'] ?? null) . PHP_EOL;
echo "bytes=" . filesize($ex['path']) . PHP_EOL;

unlink($ex['path']);
echo "smoke file removed\n";

if (!$ok) {
    fwrite(STDERR, "FAIL: payload shape or private originals in export\n");
    exit(1);
}
echo "PASS seal export v2 (no original/also in file)\n";

// Multi-part: two segments → export_SMOKE_PARTS.1.json + .2.json
$fakeWipParts = $fakeWip;
$fakeWipParts['segments'] = [
    ['title' => 'part alpha', 'from_seq' => 0, 'to_seq' => 0],
    ['title' => 'part beta', 'from_seq' => 1, 'to_seq' => 1],
];
$ex2 = logimport_export_submit('SMOKE_PARTS', $fakeCore, $fakeWipParts);
if (empty($ex2['ok']) || (int) ($ex2['parts'] ?? 0) !== 2) {
    fwrite(STDERR, 'FAIL: multi-part parts=' . ($ex2['parts'] ?? '?') . ' err=' . ($ex2['error'] ?? '') . PHP_EOL);
    exit(1);
}
$paths2 = $ex2['paths'] ?? [];
if (count($paths2) !== 2) {
    fwrite(STDERR, "FAIL: expected 2 paths\n");
    exit(1);
}
foreach ($paths2 as $pi => $pp) {
    if (!is_file($pp)) {
        fwrite(STDERR, "FAIL: missing part file $pp\n");
        exit(1);
    }
    $pj = json_decode((string) file_get_contents($pp), true);
    $wantFace = 'SMOKE_PARTS.' . ($pi + 1);
    if (($pj['face_id'] ?? '') !== $wantFace) {
        fwrite(STDERR, "FAIL: face_id want $wantFace got " . ($pj['face_id'] ?? '') . PHP_EOL);
        exit(1);
    }
    if ((int) ($pj['part'] ?? 0) !== $pi + 1 || (int) ($pj['part_count'] ?? 0) !== 2) {
        fwrite(STDERR, "FAIL: part meta\n");
        exit(1);
    }
    if (!is_array($pj['siblings'] ?? null) || count($pj['siblings']) !== 2) {
        fwrite(STDERR, "FAIL: siblings missing\n");
        exit(1);
    }
    if (strpos((string) file_get_contents($pp), 'SECRET_NAME') !== false) {
        fwrite(STDERR, "FAIL: original leaked in part\n");
        exit(1);
    }
    unlink($pp);
}
$mono = logimport_export_path('SMOKE_PARTS');
if (is_file($mono)) {
    fwrite(STDERR, "FAIL: monolith should be cleared when multi-part\n");
    exit(1);
}
echo "PASS multi-part seal (face.1 / face.2)\n";
