<?php
/**
 * Pure helpers + read-only search for nim-forester `branches`.
 * Used by t/tools/foresterSEARCH/forester-search.php and forester-search-selftest.php.
 */

declare(strict_types=1);

/**
 * Build parameterized read-only SELECT for keyword LIKE on branches.body.
 * Returns null when keyword is empty (caller must not query).
 *
 * @return array{sql: string, params: array<int, string>}|null
 */
function buildBranchesSearchSql(string $keyword): ?array
{
    $trimmed = trim($keyword);
    if ($trimmed === '') {
        return null;
    }

    return [
        'sql' => 'SELECT `id`, `log_ref`, `branch_id`, `sender`, `body` FROM `branches` WHERE `body` LIKE ? ORDER BY `id`',
        'params' => ['%' . $trimmed . '%'],
    ];
}

/**
 * Wrap every case-insensitive occurrence of $keyword in red HTML markup; escape all other text.
 */
function highlightKeywordRed(string $text, string $keyword): string
{
    $trimmed = trim($keyword);
    if ($trimmed === '') {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    $pattern = '/(' . preg_quote($trimmed, '/') . ')/iu';
    $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    if ($parts === false) {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    $out = '';
    foreach ($parts as $i => $part) {
        if ($part === '') {
            continue;
        }
        $escaped = htmlspecialchars($part, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($i % 2 === 1) {
            $out .= '<span class="kw-hit" style="color:red">' . $escaped . '</span>';
        } else {
            $out .= $escaped;
        }
    }

    return $out;
}

/**
 * Read-only PDO for nim-forester (no schema mutations).
 */
function foresterSearchGetPdo(): PDO
{
    $dsn = 'mysql:host=127.0.0.1;dbname=nim-forester;charset=utf8mb4';
    return new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

/**
 * Return all matching branch rows for keyword (no LIMIT).
 *
 * @return list<array<string, mixed>>
 */
function searchBranches(PDO $pdo, string $keyword): array
{
    $built = buildBranchesSearchSql($keyword);
    if ($built === null) {
        return [];
    }

    $stmt = $pdo->prepare($built['sql']);
    $stmt->execute($built['params']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return is_array($rows) ? $rows : [];
}

/**
 * CLI self-test for highlight + empty-keyword guard. PASS/FAIL on stdout.
 */
function foresterSearchSelfTest(): bool
{
    $ok = true;

    // Multi-occurrence highlight
    $multi = highlightKeywordRed('The Mirror reflects the mirror.', 'mirror');
    $multiHits = substr_count($multi, 'style="color:red"');
    if ($multiHits !== 2) {
        echo "foresterSearchSelfTest FAIL: multi-occurrence expected 2 red hits, got {$multiHits}\n";
        $ok = false;
    }
    if (strpos($multi, 'Mirror') === false || strpos($multi, 'mirror') === false) {
        echo "foresterSearchSelfTest FAIL: multi-occurrence missing matched text\n";
        $ok = false;
    }

    // Case-insensitive matching in highlight
    $mixed = highlightKeywordRed('MIRROR box Mirror MiRrOr', 'mirror');
    $mixedHits = substr_count($mixed, 'style="color:red"');
    if ($mixedHits !== 3) {
        echo "foresterSearchSelfTest FAIL: case-insensitive expected 3 red hits, got {$mixedHits}\n";
        $ok = false;
    }

    // HTML escape non-match regions
    $xss = highlightKeywordRed('<script>alert(1)</script> mirror', 'mirror');
    if (strpos($xss, '<script>') !== false) {
        echo "foresterSearchSelfTest FAIL: HTML not escaped\n";
        $ok = false;
    }
    if (strpos($xss, 'style="color:red"') === false) {
        echo "foresterSearchSelfTest FAIL: highlight missing after escape test\n";
        $ok = false;
    }

    // Empty keyword guard — SQL builder
    if (buildBranchesSearchSql('') !== null || buildBranchesSearchSql('   ') !== null) {
        echo "foresterSearchSelfTest FAIL: empty keyword should yield null SQL\n";
        $ok = false;
    }

    // Empty keyword guard — highlight returns escaped plain text only
    $plain = highlightKeywordRed('no highlight here', '');
    if ($plain !== 'no highlight here' || strpos($plain, 'color:red') !== false) {
        echo "foresterSearchSelfTest FAIL: empty keyword highlight guard\n";
        $ok = false;
    }

    // Non-empty SQL builder shape
    $built = buildBranchesSearchSql('Mirror');
    if ($built === null || !isset($built['sql'], $built['params'][0]) || $built['params'][0] !== '%Mirror%') {
        echo "foresterSearchSelfTest FAIL: SQL builder shape\n";
        $ok = false;
    }
    if (stripos($built['sql'] ?? '', 'DELETE') !== false
        || stripos($built['sql'] ?? '', 'UPDATE') !== false
        || stripos($built['sql'] ?? '', 'INSERT') !== false) {
        echo "foresterSearchSelfTest FAIL: SQL must be read-only SELECT\n";
        $ok = false;
    }

    echo $ok ? "foresterSearchSelfTest: PASS\n" : "foresterSearchSelfTest: FAIL\n";
    return $ok;
}