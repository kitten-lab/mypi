<?php
/**
 * postBASIC list view — ledger-backed headlines / posts.
 */
require_once __DIR__ . '/-SIG-postBASIC.php';
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$sys = defined('WORLD_ID') ? WORLD_ID : '';
$dom = defined('DOM_SLUG') ? DOM_SLUG : null;
// Show all posts for this SYS by default; room filter optional via ?all=1 only sys
$filterRoom = isset($_GET['room_only']);
$opts = [
    'sys' => $sys,
    'kind' => 'post',
    'limit' => 40,
];
if ($filterRoom && defined('ROOM_SLUG')) {
    $opts['dom'] = $dom;
    $opts['room'] = ROOM_SLUG;
} elseif ($dom) {
    $opts['dom'] = $dom;
}

// Surface list is read-only for removal. Soft/hard delete = mypi-tui (authority), not getTool.
// Optional later: getTool ViewList with a fig flag allow_surface_delete for soft-only.

$rows = [];
$error = null;
try {
    $rows = mypi_ledger_list($opts);
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$parsedownPath = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php';
$pd = null;
if (is_file($parsedownPath)) {
    require_once $parsedownPath;
    if (class_exists('Parsedown')) {
        $pd = new Parsedown();
        $pd->setSafeMode(true);
        $pd->setBreaksEnabled(true);
    }
}
?>
<section class="postbasic-ledger-list" style="max-width:42rem;">
  <h2 style="margin-top:0;">News / ledger</h2>
  <p style="opacity:0.75;font-size:0.9rem;">
    SYS <code><?= htmlspecialchars($sys) ?></code>
    <?php if ($dom): ?> · DOM <code><?= htmlspecialchars($dom) ?></code><?php endif; ?>
    · store <code>d/_LEDGER/mypi.sqlite</code>
  </p>
<p class="muted" style="opacity:0.8;font-size:0.9rem;">
  Full reports (not buried in this form):
  <a href="/chester/crates">Crates</a> ·
  <a href="/charlie/threads">Charlie</a> ·
  <a href="/satora/shelves">TPS</a>
</p>
<?php if ($error): ?>
  <p style="color:#c66;">Ledger error: <?= htmlspecialchars($error) ?></p>
<?php elseif (!$rows): ?>
  <p>No posts yet. File a headline with MakePost.</p>
<?php else: ?>
  <?php foreach ($rows as $r): ?>
    <article style="border-top:1px solid #2a4a38;padding:0.75rem 0;">
      <h3 style="margin:0 0 0.35rem;"><?= htmlspecialchars($r['topic'] ?: '(no title)') ?></h3>
      <div class="body">
        <?php
        $body = $r['body'] ?? '';
        echo $pd ? $pd->text($body) : nl2br(htmlspecialchars($body));
        ?>
      </div>
      <pre style="opacity:0.65;font-size:0.75rem;white-space:pre-wrap;"><?php
        $ts = (int) $r['ingest_unix'];
        $dt = new DateTime('@' . $ts);
        $dt->setTimezone(new DateTimeZone('America/New_York'));
        echo htmlspecialchars(
            $dt->format('Y-m-d h:i:sa T')
            . ' · ' . $r['c_uid']
            . ' · ' . ($r['sys'] . '/' . $r['dom'] . '/' . $r['room'])
            . ' · mod:' . ($r['mod'] ?: '—')
            . ' · TPS ' . ($r['t_uid'] ?: '—')
        );
      ?></pre>
    </article>
  <?php endforeach; ?>
<?php endif; ?>
</section>
