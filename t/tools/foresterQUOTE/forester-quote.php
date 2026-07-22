<?php
/**
 * Random one-sentence quote pull from nim-forester `branches`.
 *
 * Usage:
 *   C:\xampp\php\php.exe -S 127.0.0.1:8766 -t C:\xampp\htdocs\my-pocket-internet\t\tools\foresterQUOTE
 *   http://127.0.0.1:8766/forester-quote.php?q=Mirror
 *   http://127.0.0.1:8766/forester-quote.php   (random sentence, no keyword)
 */

declare(strict_types=1);

require_once __DIR__ . '/forester-quote-lib.php';

$keyword = '';
if (isset($_GET['q'])) {
    $keyword = trim((string)$_GET['q']);
} elseif (isset($_POST['q'])) {
    $keyword = trim((string)$_POST['q']);
}

// Always pull on page load (empty keyword = random from anywhere).
$sentence = null;
$meta = null;
$error = null;
$pulled = true;

try {
    $pdo = foresterQuoteGetPdo();
    $result = pullRandomQuote($pdo, $keyword);
    $sentence = $result['sentence'];
    $meta = $result['meta'];
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$keywordEsc = htmlspecialchars($keyword, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$pullAgainHref = 'forester-quote.php' . ($keyword !== '' ? ('?q=' . rawurlencode($keyword)) : '');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>nim-forester quote pull</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 1.5rem; max-width: 40rem; }
        h1 { font-size: 1.25rem; margin: 0 0 1rem; }
        form { margin-bottom: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        input[type="search"] { width: min(24rem, 100%); padding: 0.4rem 0.5rem; }
        button, .pull-again { padding: 0.4rem 0.75rem; cursor: pointer; }
        a.pull-again {
            display: inline-block;
            text-decoration: none;
            color: inherit;
            border: 1px solid #888;
            border-radius: 0.25rem;
            background: #f4f4f4;
        }
        a.pull-again:hover { background: #e8e8e8; }
        .actions { margin: 0.75rem 0 1.25rem; }
        .quote {
            border-left: 3px solid #444;
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            font-size: 1.1rem;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .meta { color: #555; font-size: 0.85rem; margin-bottom: 0.35rem; }
        .kw-hit, .kw-hit * { color: red; }
        .err { color: #a00; }
        .hint { color: #555; font-size: 0.9rem; }
        .empty { color: #666; }
    </style>
</head>
<body>
    <h1>nim-forester — quote pull</h1>

    <form method="get" action="forester-quote.php" id="quote-search-form">
        <label for="q">Keyword</label>
        <input type="search" id="q" name="q" value="<?= $keywordEsc ?>" placeholder="optional — empty = any sentence" autofocus>
        <button type="submit">Pull</button>
    </form>

    <div class="actions">
        <a class="pull-again" id="pull-again" href="<?= htmlspecialchars($pullAgainHref, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">Pull again</a>
        <span class="hint"> — re-run current keyword (or empty) for a new random sentence</span>
    </div>

    <?php if ($error !== null): ?>
        <p class="err">Database error: <?= htmlspecialchars($error, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></p>
    <?php elseif ($sentence === null): ?>
        <p class="empty">No qualifying sentence found<?= $keyword !== '' ? ' for &ldquo;' . $keywordEsc . '&rdquo;' : '' ?>.</p>
    <?php else: ?>
        <?php if (is_array($meta)): ?>
            <div class="meta">
                id <?= (int)($meta['id'] ?? 0) ?>
                <?php if (!empty($meta['log_ref'])): ?>
                    &middot; <?= htmlspecialchars((string)$meta['log_ref'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                <?php endif; ?>
                <?php if (!empty($meta['branch_id'])): ?>
                    &middot; <?= htmlspecialchars((string)$meta['branch_id'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                <?php endif; ?>
                <?php if (!empty($meta['sender'])): ?>
                    &middot; <?= htmlspecialchars((string)$meta['sender'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <blockquote class="quote" id="quote-result">
            <?= foresterQuoteHighlightKeyword((string)$sentence, $keyword) ?>
        </blockquote>
        <?php if ($keyword !== ''): ?>
            <p class="hint">Keyword: &ldquo;<?= $keywordEsc ?>&rdquo;</p>
        <?php else: ?>
            <p class="hint">No keyword — random sentence from anywhere in branches.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
