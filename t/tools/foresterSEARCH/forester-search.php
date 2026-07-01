<?php
/**
 * Lightweight keyword search UI for nim-forester `branches`.
 *
 * Usage:
 *   C:\xampp\php\php.exe -S 127.0.0.1:8765 -t C:\xampp\htdocs\my-pocket-internet\t\tools\foresterSEARCH
 *   http://127.0.0.1:8765/forester-search.php?q=Mirror
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-search-lib.php';

$keyword = '';
if (isset($_GET['q'])) {
    $keyword = trim((string)$_GET['q']);
} elseif (isset($_POST['q'])) {
    $keyword = trim((string)$_POST['q']);
}

$results = [];
$error = null;
$searched = $keyword !== '';

if ($searched) {
    try {
        $pdo = foresterSearchGetPdo();
        $results = searchBranches($pdo, $keyword);
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>nim-forester branches search</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 1.5rem; max-width: 52rem; }
        h1 { font-size: 1.25rem; margin: 0 0 1rem; }
        form { margin-bottom: 1.25rem; }
        input[type="search"] { width: min(24rem, 100%); padding: 0.4rem 0.5rem; }
        button { padding: 0.4rem 0.75rem; cursor: pointer; }
        .meta { color: #555; font-size: 0.85rem; margin-bottom: 0.35rem; }
        .result { border-top: 1px solid #ddd; padding: 0.75rem 0; }
        .body { white-space: pre-wrap; word-break: break-word; }
        .kw-hit, .kw-hit * { color: red; }
        .err { color: #a00; }
        .count { color: #555; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>nim-forester — branches keyword search</h1>

    <form method="get" action="">
        <label for="q">Keyword</label>
        <input type="search" id="q" name="q" value="<?= htmlspecialchars($keyword, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>" autofocus>
        <button type="submit">Search</button>
    </form>

    <?php if ($error !== null): ?>
        <p class="err">Database error: <?= htmlspecialchars($error, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></p>
    <?php elseif ($searched): ?>
        <p class="count"><?= count($results) ?> matching branch<?= count($results) === 1 ? '' : 'es' ?> for &ldquo;<?= htmlspecialchars($keyword, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>&rdquo;</p>
        <?php if (count($results) === 0): ?>
            <p>No matches.</p>
        <?php else: ?>
            <?php foreach ($results as $row): ?>
                <article class="result">
                    <div class="meta">
                        id <?= (int)($row['id'] ?? 0) ?>
                        &middot; <?= htmlspecialchars((string)($row['log_ref'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        <?php if (!empty($row['branch_id'])): ?>
                            &middot; <?= htmlspecialchars((string)$row['branch_id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        <?php endif; ?>
                        <?php if (!empty($row['sender'])): ?>
                            &middot; <?= htmlspecialchars((string)$row['sender'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                    <div class="body"><?= highlightKeywordRed((string)($row['body'] ?? ''), $keyword) ?></div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>