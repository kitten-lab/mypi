<?php
/**
 * CHESTER · Crates report — tool page: echo OK (t/tools).
 * m/doors only register getTool; sky stays in the door file.
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$sys = defined('WORLD_ID') ? WORLD_ID : null;
$opts = ['limit' => 80];
if (isset($_GET['here']) && $sys) {
    $opts['sys'] = $sys;
}
$rows = [];
$err = null;
try {
    $rows = mypi_ledger_list($opts);
} catch (Throwable $e) {
    $err = $e->getMessage();
}
?>
<section class="ledger-report crates-report">
  <h2>CHESTER · Crates</h2>
  <p class="muted">First-class crate browser (d/_CHESTER sense). Soft-deleted hidden.</p>
  <p class="muted"><a href="?here=1">This SYS only</a> · <a href="?">All systems</a></p>
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err) ?></p>
<?php elseif (!$rows): ?>
  <p>No crates yet.</p>
<?php else: ?>
  <table class="ledger-table">
    <thead>
      <tr>
        <th>c_uid</th>
        <th>topic</th>
        <th>sys/dom/room</th>
        <th>mod</th>
        <th>TPS window</th>
        <th>event_unix</th>
        <th>ingest</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><code><?= htmlspecialchars($r['c_uid']) ?></code></td>
        <td><?= htmlspecialchars($r['topic'] ?: '(no title)') ?></td>
        <td><code><?= htmlspecialchars(($r['sys'] ?? '') . '/' . ($r['dom'] ?? '') . '/' . ($r['room'] ?? '')) ?></code></td>
        <td><?= htmlspecialchars($r['mod'] ?? '') ?></td>
        <td><code><?= htmlspecialchars($r['t_uid'] ?? '') ?></code></td>
        <td><code><?= (int) ($r['event_unix'] ?? 0) ?></code></td>
        <td><?= (int) $r['ingest_unix'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</section>
<!-- styles: t/tools/ledgerREPORT/ledgerREPORT.css via getTool loadTool_Style -->

