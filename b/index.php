<?php
/**
 * Port b gate — list SYSes (folders with index.php). Prefer pocket-browser launcher.
 */
header('Content-Type: text/html; charset=utf-8');
$root = __DIR__;
$systems = [];
foreach (scandir($root) as $name) {
    if ($name === '.' || $name === '..' || $name[0] === '-' || $name === 'index.php') {
        continue;
    }
    if ($name === '--archive') {
        continue;
    }
    if (is_dir($root . '/' . $name) && is_file($root . '/' . $name . '/index.php')) {
        $systems[] = $name;
    }
}
sort($systems);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>b — surface gate</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #0a100e; color: #b8e0c8; padding: 2rem; }
    a { color: #3dcf7a; }
    code { color: #8fd4a8; }
    li { margin: 0.35rem 0; }
  </style>
</head>
<body>
  <h1>Port <code>b</code></h1>
  <p>All surfaces live under <code>http://b/{sys}/…</code> — no new hosts per surface.</p>
  <ul>
  <?php foreach ($systems as $sys): ?>
    <li><a href="/<?= htmlspecialchars($sys) ?>/"><?= htmlspecialchars($sys) ?></a></li>
  <?php endforeach; ?>
  </ul>
</body>
</html>
