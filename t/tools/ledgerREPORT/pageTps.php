<?php
/**
 * SATORA · Nearby windows — tool page: echo OK.
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$shelves = [];
$selected = isset($_GET['tps']) ? (string) $_GET['tps'] : '';
$crates = [];
$err = null;
$windowSec = 900;
$shelfMeta = null;
try {
    $shelves = mypi_ledger_list_tps(60);
    $pdo = mypi_ledger_pdo();
    $windowSec = mypi_ledger_tps_window_seconds($pdo);
    if ($selected !== '') {
        $crates = mypi_ledger_tps_crates($selected);
        $st2 = $pdo->prepare('SELECT * FROM tps_shelves WHERE tps_uid = ?');
        $st2->execute([$selected]);
        $shelfMeta = $st2->fetch() ?: null;
    }
} catch (Throwable $e) {
    $err = $e->getMessage();
}
?>
<section class="ledger-report tps-report">
  <h2>SATORA · Nearby (TPS windows)</h2>
  <p class="muted">
    Windowed membrane time (default <?= (int) $windowSec ?>s) —
    <em>crates that happened nearby</em>, not every-second research shelves.
    Exact <code>event_unix</code> on each crate; open a window to sort by that order.
  </p>
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err) ?></p>
<?php elseif (!$shelves): ?>
  <p>No windows yet. Post from News to open one.</p>
<?php else: ?>
  <table class="ledger-table">
    <thead>
      <tr>
        <th>tps_uid</th>
        <th>window_unix</th>
        <th>width</th>
        <th>crates</th>
        <th>clock</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($shelves as $s): ?>
      <tr class="<?= $selected === $s['tps_uid'] ? 'sel' : '' ?>">
        <td><code><?= htmlspecialchars($s['tps_uid']) ?></code></td>
        <td><code><?= (int) $s['window_unix'] ?></code></td>
        <td><?= (int) $s['window_seconds'] ?>s</td>
        <td><?= (int) $s['n_crates'] ?></td>
        <td><?= htmlspecialchars($s['clock_id'] ?? 'gaia') ?></td>
        <td><a href="?tps=<?= rawurlencode($s['tps_uid']) ?>">open</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($selected !== ''): ?>
    <h3>Events nearby in <code><?= htmlspecialchars($selected) ?></code></h3>
    <?php if ($shelfMeta): ?>
      <p class="muted">
        window_unix <code><?= (int) $shelfMeta['window_unix'] ?></code>
        · span
        <code><?= (int) $shelfMeta['window_unix'] ?></code>–
        <code><?= (int) $shelfMeta['window_unix'] + (int) $shelfMeta['window_seconds'] - 1 ?></code>
      </p>
    <?php endif; ?>
    <?php if (!$crates): ?>
      <p>Empty window.</p>
    <?php else: ?>
      <table class="ledger-table">
        <thead>
          <tr>
            <th>#</th>
            <th>event_unix</th>
            <th>ingest_unix</th>
            <th>c_uid</th>
            <th>topic</th>
            <th>sys/dom/room</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($crates as $i => $c): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><code><?= (int) $c['event_unix'] ?></code></td>
            <td><code><?= (int) $c['ingest_unix'] ?></code></td>
            <td><code><?= htmlspecialchars($c['c_uid']) ?></code></td>
            <td><?= htmlspecialchars($c['topic'] ?: '(no title)') ?></td>
            <td><code><?= htmlspecialchars(($c['sys'] ?? '') . '/' . ($c['dom'] ?? '') . '/' . ($c['room'] ?? '')) ?></code></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>
</section>
<!-- styles: t/tools/ledgerREPORT/ledgerREPORT.css via getTool loadTool_Style -->

