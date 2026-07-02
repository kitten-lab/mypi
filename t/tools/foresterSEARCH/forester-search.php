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
require_once __DIR__ . '/parsedown/Parsedown.php'; 


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
        body { font-family: sans-serif; max-width: 800px; margin: 1rem auto; padding: 0 1rem; }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        form { margin-bottom: 1rem; }
        input[type="search"] { width: 100%; padding: 0.5rem; font-size: 1rem; }
        button { padding: 0.5rem 1rem; font-size: 1rem; }
        p { margin: 0.5rem 0; background: #f9f9f9; padding: 0.5rem; border-radius: 4px; }
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
            <?php foreach ($results as $row): 
            $Parsedown = new Parsedown();
                $timestamp = htmlspecialchars((string)($row['unix_ts'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $date = new DateTime("@$timestamp");
                ?>
                <article class="result">
                    <div class="meta">
                        <?= $date->format('M j, Y @ H:i:s') ?> &middot;
                        id <?= (int)($row['id'] ?? 0) ?>
                        &middot; 
                        <?php if (!empty($row['branch_id'])): ?>
                            <?= htmlspecialchars((string)$row['branch_id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        <?php endif; ?>
                        <?php if (!empty($row['sender'])): ?>
                            &middot; <?= htmlspecialchars((string)$row['sender'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                    <div class="body"><?= $Parsedown->text(highlightKeywordRed(($row['body'] ?? ''), $keyword)) ?></div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>