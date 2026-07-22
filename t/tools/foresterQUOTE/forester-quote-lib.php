<?php
/**
 * Pure helpers + read-only random quote pull for nim-forester `branches`.
 * Used by forester-quote.php, forester-quote-selftest.php, forester-quote-db-probe.php.
 *
 * Sentence boundary: split on . ? ! followed by whitespace or end-of-string.
 * Perfect NLP is out of scope; this is intentionally simple.
 */

declare(strict_types=1);

/**
 * Split body text into sentences (period / question / exclamation boundaries).
 * Empty segments after trim are dropped.
 *
 * @return list<string>
 */
function foresterQuoteSplitSentences(string $text): array
{
    $text = trim($text);
    if ($text === '') {
        return [];
    }

    // Split after . ? ! when followed by whitespace or end. Keep delimiter on the left piece.
    $parts = preg_split('/(?<=[.?!])(?:\s+|$)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    if ($parts === false) {
        return $text !== '' ? [$text] : [];
    }

    $out = [];
    foreach ($parts as $part) {
        $s = trim((string)$part);
        if ($s !== '') {
            $out[] = $s;
        }
    }

    return $out;
}

/**
 * Keep only sentences that contain $keyword (case-insensitive).
 * Empty keyword means all sentences qualify.
 *
 * @param list<string> $sentences
 * @return list<string>
 */
function foresterQuoteFilterSentencesByKeyword(array $sentences, string $keyword): array
{
    $trimmed = trim($keyword);
    if ($trimmed === '') {
        return array_values($sentences);
    }

    $out = [];
    foreach ($sentences as $sentence) {
        if (mb_stripos((string)$sentence, $trimmed) !== false) {
            $out[] = (string)$sentence;
        }
    }

    return $out;
}

/**
 * Pick one random sentence from candidates, or null if empty.
 * Optional $rng returns an int in [0, $maxInclusive] for testability.
 *
 * @param list<string> $candidates
 * @param callable(int): int|null $rng
 */
function foresterQuotePickRandomSentence(array $candidates, ?callable $rng = null): ?string
{
    $n = count($candidates);
    if ($n === 0) {
        return null;
    }

    if ($rng === null) {
        $idx = random_int(0, $n - 1);
    } else {
        $idx = (int)$rng($n - 1);
        if ($idx < 0) {
            $idx = 0;
        }
        if ($idx > $n - 1) {
            $idx = $n - 1;
        }
    }

    return (string)$candidates[$idx];
}

/**
 * From raw body text, extract qualifying sentences and pick one at random.
 *
 * @param callable(int): int|null $rng
 */
function foresterQuoteSentenceFromBody(string $body, string $keyword = '', ?callable $rng = null): ?string
{
    $sentences = foresterQuoteSplitSentences($body);
    $filtered = foresterQuoteFilterSentencesByKeyword($sentences, $keyword);
    return foresterQuotePickRandomSentence($filtered, $rng);
}

/**
 * Build read-only SELECT for candidate branch bodies.
 * Non-empty keyword → LIKE filter; empty → all non-empty bodies.
 *
 * @return array{sql: string, params: array<int, string>}
 */
function buildBranchesQuoteSql(string $keyword): array
{
    $trimmed = trim($keyword);
    if ($trimmed === '') {
        return [
            'sql' => 'SELECT `id`, `log_ref`, `branch_id`, `sender`, `body` FROM `branches` WHERE `body` IS NOT NULL AND TRIM(`body`) <> \'\' ORDER BY `id`',
            'params' => [],
        ];
    }

    return [
        'sql' => 'SELECT `id`, `log_ref`, `branch_id`, `sender`, `body` FROM `branches` WHERE `body` LIKE ? ORDER BY `id`',
        'params' => ['%' . $trimmed . '%'],
    ];
}

/**
 * Wrap every case-insensitive occurrence of $keyword in red HTML; escape all other text.
 */
function foresterQuoteHighlightKeyword(string $text, string $keyword): string
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
function foresterQuoteGetPdo(): PDO
{
    $dsn = 'mysql:host=127.0.0.1;dbname=nim-forester;charset=utf8mb4';
    return new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

/**
 * Collect qualifying sentences from DB branch bodies, then pick one at random.
 *
 * @return array{sentence: ?string, meta: ?array<string, mixed>}
 */
function pullRandomQuote(PDO $pdo, string $keyword = '', ?callable $rng = null): array
{
    $built = buildBranchesQuoteSql($keyword);
    $stmt = $pdo->prepare($built['sql']);
    $stmt->execute($built['params']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!is_array($rows)) {
        $rows = [];
    }

    /** @var list<array{sentence: string, meta: array<string, mixed>}> $candidates */
    $candidates = [];
    $kw = trim($keyword);

    foreach ($rows as $row) {
        $body = (string)($row['body'] ?? '');
        $sentences = foresterQuoteSplitSentences($body);
        $filtered = foresterQuoteFilterSentencesByKeyword($sentences, $kw);
        foreach ($filtered as $sentence) {
            $candidates[] = [
                'sentence' => $sentence,
                'meta' => [
                    'id' => $row['id'] ?? null,
                    'log_ref' => $row['log_ref'] ?? null,
                    'branch_id' => $row['branch_id'] ?? null,
                    'sender' => $row['sender'] ?? null,
                ],
            ];
        }
    }

    $n = count($candidates);
    if ($n === 0) {
        return ['sentence' => null, 'meta' => null];
    }

    if ($rng === null) {
        $idx = random_int(0, $n - 1);
    } else {
        $idx = (int)$rng($n - 1);
        if ($idx < 0) {
            $idx = 0;
        }
        if ($idx > $n - 1) {
            $idx = $n - 1;
        }
    }

    return [
        'sentence' => $candidates[$idx]['sentence'],
        'meta' => $candidates[$idx]['meta'],
    ];
}

/**
 * CLI self-test for sentence split, filter, pick, SQL read-only, highlight. PASS/FAIL on stdout.
 */
function foresterQuoteSelfTest(): bool
{
    $ok = true;

    // Sentence split on . ? !
    $split = foresterQuoteSplitSentences('Hello world. How are you? Fine! Trailing');
    if (count($split) !== 4
        || $split[0] !== 'Hello world.'
        || $split[1] !== 'How are you?'
        || $split[2] !== 'Fine!'
        || $split[3] !== 'Trailing') {
        echo "foresterQuoteSelfTest FAIL: sentence split unexpected: " . json_encode($split) . "\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: sentence split (4 parts)\n";
    }

    // Empty body → no sentences
    if (foresterQuoteSplitSentences('') !== [] || foresterQuoteSplitSentences('   ') !== []) {
        echo "foresterQuoteSelfTest FAIL: empty body should yield no sentences\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: empty body yields no sentences\n";
    }

    // Keyword filter (case-insensitive); empty keyword = all
    $pool = ['Alpha beta gamma.', 'The Mirror reflects.', 'No match here.'];
    $withKw = foresterQuoteFilterSentencesByKeyword($pool, 'mirror');
    if (count($withKw) !== 1 || $withKw[0] !== 'The Mirror reflects.') {
        echo "foresterQuoteSelfTest FAIL: keyword filter expected one Mirror sentence\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: keyword filter (case-insensitive)\n";
    }

    $all = foresterQuoteFilterSentencesByKeyword($pool, '');
    if (count($all) !== 3) {
        echo "foresterQuoteSelfTest FAIL: empty keyword should keep all sentences\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: empty keyword keeps all sentences\n";
    }

    // Random pick with fixed rng
    $picked = foresterQuotePickRandomSentence(['a', 'b', 'c'], static function (int $max): int {
        return 1;
    });
    if ($picked !== 'b') {
        echo "foresterQuoteSelfTest FAIL: pick random with rng expected b, got " . var_export($picked, true) . "\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: pick random with injected rng\n";
    }

    if (foresterQuotePickRandomSentence([]) !== null) {
        echo "foresterQuoteSelfTest FAIL: empty candidates should return null\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: empty candidates return null\n";
    }

    // Body → one sentence with keyword via fixed rng
    $body = 'First sentence. Second has Keyword here. Third sentence.';
    $fromBody = foresterQuoteSentenceFromBody($body, 'keyword', static function (int $max): int {
        return 0;
    });
    if ($fromBody === null || mb_stripos($fromBody, 'keyword') === false) {
        echo "foresterQuoteSelfTest FAIL: sentenceFromBody keyword path\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: sentenceFromBody keyword path\n";
    }

    $anyFromBody = foresterQuoteSentenceFromBody($body, '', static function (int $max): int {
        return 2;
    });
    if ($anyFromBody !== 'Third sentence.') {
        echo "foresterQuoteSelfTest FAIL: sentenceFromBody empty keyword path got " . var_export($anyFromBody, true) . "\n";
        $ok = false;
    } else {
        echo "foresterQuoteSelfTest PASS: sentenceFromBody empty keyword path\n";
    }

    // SQL builders: SELECT-only; empty vs keyword shapes
    $emptyBuilt = buildBranchesQuoteSql('');
    $kwBuilt = buildBranchesQuoteSql('Mirror');
    if (!isset($emptyBuilt['sql'], $kwBuilt['sql'], $kwBuilt['params'][0])) {
        echo "foresterQuoteSelfTest FAIL: SQL builder shape missing keys\n";
        $ok = false;
    } else {
        foreach ([$emptyBuilt['sql'], $kwBuilt['sql']] as $sql) {
            if (stripos($sql, 'SELECT') === false
                || stripos($sql, 'DELETE') !== false
                || stripos($sql, 'UPDATE') !== false
                || stripos($sql, 'INSERT') !== false) {
                echo "foresterQuoteSelfTest FAIL: SQL must be read-only SELECT: {$sql}\n";
                $ok = false;
            }
        }
        if (($kwBuilt['params'][0] ?? null) !== '%Mirror%') {
            echo "foresterQuoteSelfTest FAIL: keyword LIKE param shape\n";
            $ok = false;
        }
        if ($emptyBuilt['params'] !== []) {
            echo "foresterQuoteSelfTest FAIL: empty keyword should have no params\n";
            $ok = false;
        }
        if ($ok) {
            echo "foresterQuoteSelfTest PASS: SQL builders SELECT-only\n";
        }
    }

    // Highlight escape + keyword
    $xss = foresterQuoteHighlightKeyword('<script>alert(1)</script> mirror', 'mirror');
    if (strpos($xss, '<script>') !== false) {
        echo "foresterQuoteSelfTest FAIL: HTML not escaped\n";
        $ok = false;
    }
    if (strpos($xss, 'style="color:red"') === false) {
        echo "foresterQuoteSelfTest FAIL: highlight missing after escape test\n";
        $ok = false;
    }
    $plain = foresterQuoteHighlightKeyword('no highlight here', '');
    if ($plain !== 'no highlight here' || strpos($plain, 'color:red') !== false) {
        echo "foresterQuoteSelfTest FAIL: empty keyword highlight guard\n";
        $ok = false;
    }
    if (strpos($xss, '<script>') === false && strpos($xss, 'style="color:red"') !== false && $plain === 'no highlight here') {
        echo "foresterQuoteSelfTest PASS: highlight escape + empty guard\n";
    }

    echo $ok ? "foresterQuoteSelfTest: PASS\n" : "foresterQuoteSelfTest: FAIL\n";
    return $ok;
}
