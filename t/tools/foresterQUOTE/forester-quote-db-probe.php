<?php
/**
 * Read-only integration probe: pull one random sentence (keyword or empty).
 *
 * Usage:
 *   C:\xampp\php\php.exe t\tools\foresterQUOTE\forester-quote-db-probe.php [keyword]
 *   C:\xampp\php\php.exe t\tools\foresterQUOTE\forester-quote-db-probe.php
 *   C:\xampp\php\php.exe t\tools\foresterQUOTE\forester-quote-db-probe.php --twice [keyword]
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-quote-lib.php';

$args = array_slice($argv, 1);
$twice = false;
$keyword = '';
foreach ($args as $arg) {
    if ($arg === '--twice') {
        $twice = true;
        continue;
    }
    $keyword = (string)$arg;
}

try {
    $pdo = foresterQuoteGetPdo();

    $built = buildBranchesQuoteSql($keyword);
    $sql = $built['sql'] ?? '';
    if (stripos($sql, 'SELECT') === false
        || stripos($sql, 'DELETE') !== false
        || stripos($sql, 'UPDATE') !== false
        || stripos($sql, 'INSERT') !== false) {
        echo "probe FAIL: non-read-only SQL\n";
        exit(1);
    }

    $runs = $twice ? 2 : 1;
    $sentences = [];
    for ($i = 0; $i < $runs; $i++) {
        $result = pullRandomQuote($pdo, $keyword);
        $sentence = $result['sentence'];
        echo "probe run " . ($i + 1) . " keyword: " . ($keyword === '' ? '(empty)' : $keyword) . "\n";
        if ($sentence === null) {
            echo "empty_set: no qualifying sentence\n";
            echo "sentence_count: 0\n";
        } else {
            // Exactly one sentence result — never a multi-row dump
            echo "sentence_count: 1\n";
            echo "sentence: " . $sentence . "\n";
            if ($keyword !== '' && mb_stripos($sentence, $keyword) === false) {
                echo "probe FAIL: sentence does not contain keyword\n";
                exit(1);
            }
            $sentences[] = $sentence;
        }
        if (is_array($result['meta'])) {
            echo "meta_id: " . json_encode($result['meta']['id'] ?? null) . "\n";
        }
    }

    if ($twice && count($sentences) === 2) {
        echo "two_pulls_same: " . ($sentences[0] === $sentences[1] ? 'yes' : 'no') . "\n";
        echo "two_pulls_both_single: yes\n";
    }

    echo "probe: PASS\n";
    exit(0);
} catch (Throwable $e) {
    echo "probe CONNECTION_FAIL: " . $e->getMessage() . "\n";
    exit(2);
}
