<?php
/**
 * Read-only integration probe: search branches for a known keyword.
 *
 * Usage:
 *   C:\xampp\php\php.exe z\forester-search-db-probe.php [keyword]
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-search-lib.php';

$keyword = $argv[1] ?? 'Mirror';

try {
    $pdo = foresterSearchGetPdo();
    $rows = searchBranches($pdo, $keyword);
    echo "probe keyword: {$keyword}\n";
    echo "row_count: " . count($rows) . "\n";
    $shown = 0;
    foreach ($rows as $row) {
        if ($shown >= 3) {
            break;
        }
        echo json_encode([
            'id' => $row['id'] ?? null,
            'body' => mb_substr((string)($row['body'] ?? ''), 0, 120),
        ], JSON_UNESCAPED_UNICODE) . "\n";
        $shown++;
    }
    $built = buildBranchesSearchSql($keyword);
    $sql = $built['sql'] ?? '';
    if (stripos($sql, 'SELECT') === false || stripos($sql, 'DELETE') !== false || stripos($sql, 'UPDATE') !== false || stripos($sql, 'INSERT') !== false) {
        echo "probe FAIL: non-read-only SQL\n";
        exit(1);
    }
    if (count($rows) < 1) {
        echo "probe FAIL: no rows for keyword\n";
        exit(1);
    }
    echo "probe: PASS\n";
    exit(0);
} catch (Throwable $e) {
    echo "probe CONNECTION_FAIL: " . $e->getMessage() . "\n";
    exit(2);
}