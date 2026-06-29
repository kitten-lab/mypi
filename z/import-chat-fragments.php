<?php
/**
 * Standalone PHP import script: ChatGPT JSON logs (from z/logs/) -> idempotent chat_fragments MySQL table.
 *
 * - Scans only *.json inside the logs/ directory sibling to this script (or resolved via __DIR__).
 * - Parses the "mapping" tree structure per ChatGPT export format.
 * - Extracts fragments keeping: timestamp (create_time as raw float/DOUBLE), source (chat filename), speaker (author.role), content (parts[0] text).
 * - Skips messages without usable non-empty string text content.
 * - Creates DB `pocket_internet`, table `chat_fragments` + basic indexes (source, speaker, FULLTEXT content) if absent.
 * - Idempotent: per-source DELETE then INSERT (safe to re-run; no duplicate fragments for same source).
 * - Pure function extractFragments() for the walker (unit tested in-script).
 *
 * Usage:
 *   C:\xampp\php\php.exe z\import-chat-fragments.php
 *
 * DB: default XAMPP root/'' @ localhost, DB=pocket_internet (created if needed).
 * No external includes, no config files, no CLI flags required for normal run.
 */

declare(strict_types=1);

function extractFragments(string $absPath, string $filename): array
{
    $json = @file_get_contents($absPath);
    if ($json === false || $json === '') {
        return [];
    }
    $data = json_decode($json, true);
    if (!is_array($data) || !isset($data['mapping']) || !is_array($data['mapping'])) {
        return [];
    }
    $mapping = $data['mapping'];

    // Find root node (parent === null)
    $root = null;
    foreach ($mapping as $node) {
        if (array_key_exists('parent', $node) && $node['parent'] === null) {
            $root = $node;
            break;
        }
    }
    if ($root === null) {
        return [];
    }

    $frags = [];
    $current = $root;
    while ($current !== null) {
        $msg = $current['message'] ?? null;
        if (is_array($msg)) {
            $author = $msg['author'] ?? null;
            $content = $msg['content'] ?? null;
            $role = is_array($author) && isset($author['role']) ? (string)$author['role'] : null;
            $parts = is_array($content) && isset($content['parts']) && is_array($content['parts']) ? $content['parts'] : [];
            $part0 = $parts[0] ?? null;

            if ($role !== null && is_string($part0) && trim($part0) !== '') {
                $ts = isset($msg['create_time']) ? $msg['create_time'] : null;
                // store raw value (float or null) - caller/DB layer handles
                $frags[] = [
                    'timestamp' => $ts,
                    'source' => $filename,
                    'speaker' => $role,
                    'content' => $part0,
                ];
            }
        }

        $children = $current['children'] ?? [];
        if (is_array($children) && count($children) > 0) {
            $nextId = $children[0];
            $current = isset($mapping[$nextId]) ? $mapping[$nextId] : null;
        } else {
            $current = null;
        }
    }

    return $frags;
}

function getPdo(): PDO
{
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $dbName = 'pocket_internet';

    // Connect without DB first to allow CREATE DATABASE
    $dsnNoDb = "mysql:host={$host};charset=utf8mb4";
    $pdo = new PDO($dsnNoDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");

    return $pdo;
}

function ensureSchema(PDO $pdo): void
{
    // Table
    $createTable = <<<'SQL'
CREATE TABLE IF NOT EXISTS `chat_fragments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `timestamp` DOUBLE NULL,
    `source` VARCHAR(255) NOT NULL,
    `speaker` VARCHAR(50) NOT NULL,
    `content` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;
    $pdo->exec($createTable);

    // Indexes (MariaDB 10.4 compatible: no "IF NOT EXISTS" for indexes in all contexts; swallow duplicate errors)
    $indexes = [
        "CREATE INDEX `idx_source` ON `chat_fragments` (`source`)",
        "CREATE INDEX `idx_speaker` ON `chat_fragments` (`speaker`)",
        "CREATE FULLTEXT INDEX `idx_content_fulltext` ON `chat_fragments` (`content`)",
    ];
    foreach ($indexes as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // 1061 = duplicate key name (already exists) or 1068 etc. Safe to ignore for idempotency of schema.
            $msg = $e->getMessage();
            if (strpos($msg, '1061') === false && strpos($msg, 'Duplicate key name') === false) {
                // rethrow unexpected
                throw $e;
            }
        }
    }
}

function importFragments(PDO $pdo, string $source, array $frags): void
{
    if (count($frags) === 0) {
        return;
    }

    // Idempotency: clear previous rows for this exact source (filename)
    $delStmt = $pdo->prepare("DELETE FROM `chat_fragments` WHERE `source` = ?");
    $delStmt->execute([$source]);

    // Prepared INSERT (batched via loop)
    $insStmt = $pdo->prepare(
        "INSERT INTO `chat_fragments` (`timestamp`, `source`, `speaker`, `content`) VALUES (?, ?, ?, ?)"
    );

    foreach ($frags as $f) {
        $ts = $f['timestamp'];
        // ensure numeric or null for DOUBLE
        if ($ts !== null && !is_numeric($ts)) {
            $ts = null;
        }
        $insStmt->execute([
            $ts,
            $f['source'],
            $f['speaker'],
            $f['content'],
        ]);
    }
}

function main(): void
{
    // Resolve logs directory relative to this script using __DIR__
    // Preferred: script placed in z/ => __DIR__ . '/logs'
    // Fallbacks for flexibility (script in project root or elsewhere)
    $candidates = [
        __DIR__ . DIRECTORY_SEPARATOR . 'logs',
        __DIR__ . DIRECTORY_SEPARATOR . 'z' . DIRECTORY_SEPARATOR . 'logs',
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'z' . DIRECTORY_SEPARATOR . 'logs',
    ];
    $logsDir = null;
    foreach ($candidates as $cand) {
        if (is_dir($cand)) {
            $logsDir = $cand;
            break;
        }
    }
    if ($logsDir === null) {
        fwrite(STDERR, "ERROR: Could not locate z/logs/ directory relative to script.\n");
        exit(1);
    }

    $pattern = $logsDir . DIRECTORY_SEPARATOR . '*.json';
    $files = glob($pattern);
    if ($files === false) {
        $files = [];
    }

    // Sort for deterministic order
    sort($files);

    $pdo = getPdo();
    ensureSchema($pdo);

    $processed = 0;
    $totalFragments = 0;
    $skippedEmpty = 0;

    echo "Starting import from: {$logsDir}\n";
    echo "Found " . count($files) . " json file(s).\n";

    foreach ($files as $absPath) {
        $filename = basename($absPath);
        if ($filename === 'conversations.json') {
            echo "Skipping root conversations.json\n";
            continue;
        }

        $frags = extractFragments($absPath, $filename);
        importFragments($pdo, $filename, $frags);

        $processed++;
        $totalFragments += count($frags);
        echo "file {$filename} -> " . count($frags) . " fragments\n";
    }

    echo "\nDone. processed {$processed} jsons, total fragments: {$totalFragments}\n";

    // quick count from DB for confirmation
    $cntStmt = $pdo->query("SELECT COUNT(*) AS c FROM `chat_fragments`");
    $row = $cntStmt->fetch();
    echo "DB total rows now: " . ($row['c'] ?? 0) . "\n";
}

// In-script unit test for extractFragments walker (per plan: "Implement and unit-test-in-script")
function runSelfTest(): bool
{
    $sample = <<<'EOT'
{
  "title": "selftest",
  "create_time": 0,
  "mapping": {
    "root-uuid": {
      "id": "root-uuid",
      "message": null,
      "parent": null,
      "children": ["msg-user"]
    },
    "msg-user": {
      "id": "msg-user",
      "message": {
        "id": "msg-user",
        "author": { "role": "user", "name": null, "metadata": {} },
        "create_time": 1739217517.288584,
        "update_time": null,
        "content": {
          "content_type": "text",
          "parts": [ "Hello from user" ]
        },
        "status": "finished_successfully"
      },
      "parent": "root-uuid",
      "children": ["msg-assistant"]
    },
    "msg-assistant": {
      "id": "msg-assistant",
      "message": {
        "id": "msg-assistant",
        "author": { "role": "assistant", "name": null, "metadata": {} },
        "create_time": 1739217521.756234,
        "update_time": null,
        "content": {
          "content_type": "text",
          "parts": [ "Hello back from assistant with some longer text content for testing." ]
        },
        "status": "finished_successfully"
      },
      "parent": "msg-user",
      "children": []
    },
    "msg-hidden": {
      "id": "msg-hidden",
      "message": {
        "id": "msg-hidden",
        "author": { "role": "system", "name": null, "metadata": {} },
        "create_time": null,
        "content": { "content_type": "text", "parts": [ "" ] },
        "status": "finished_successfully"
      },
      "parent": "msg-assistant",
      "children": []
    }
  }
}
EOT;

    $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'chatgpt_selftest_' . uniqid('', true) . '.json';
    if (file_put_contents($tmpFile, $sample) === false) {
        echo "selfTest: FAIL (could not write tmp)\n";
        return false;
    }

    $frags = extractFragments($tmpFile, 'selftest.json');
    @unlink($tmpFile);

    $ok = true;
    $checks = [];

    if (count($frags) !== 2) {
        $ok = false;
        $checks[] = "count=" . count($frags) . " != 2";
    }
    if ($ok && $frags[0]['source'] !== 'selftest.json') {
        $ok = false; $checks[] = "source mismatch";
    }
    if ($ok && $frags[0]['speaker'] !== 'user') {
        $ok = false; $checks[] = "speaker0 != user";
    }
    if ($ok && $frags[0]['content'] !== 'Hello from user') {
        $ok = false; $checks[] = "content0 mismatch";
    }
    if ($ok && abs((float)$frags[0]['timestamp'] - 1739217517.288584) > 0.0001) {
        $ok = false; $checks[] = "ts0 mismatch";
    }
    if ($ok && $frags[1]['speaker'] !== 'assistant') {
        $ok = false; $checks[] = "speaker1 != assistant";
    }
    if ($ok && strpos($frags[1]['content'], 'Hello back from assistant') !== 0) {
        $ok = false; $checks[] = "content1 mismatch";
    }
    if ($ok && $frags[1]['timestamp'] === null) {
        $ok = false; $checks[] = "ts1 should not be null";
    }

    if ($ok) {
        echo "selfTestExtract: OK (2 frags, correct fields, skips empty)\n";
        return true;
    } else {
        echo "selfTestExtract: FAIL " . implode('; ', $checks) . "\n";
        return false;
    }
}

// Entry point: run self-test (in-script unit test) then main ONLY when invoked directly as the CLI script.
// Using realpath compare prevents side effects (test+main) when the file is require()'d for testing.
if (php_sapi_name() === 'cli') {
    $self = realpath(__FILE__);
    $invoked = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : null;
    if ($self === $invoked) {
        $selfTestPassed = runSelfTest();
        if (!$selfTestPassed) {
            fwrite(STDERR, "Self-test failed. Aborting import.\n");
            exit(1);
        }

        try {
            main();
        } catch (Throwable $e) {
            fwrite(STDERR, "FATAL: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
            exit(1);
        }
        exit(0);
    }
}
