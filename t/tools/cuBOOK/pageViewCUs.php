<?php
/**
 * List guestbook crates for this place (kind=guestcu).
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php';

$place = mypi_ledger_place_from_sky();
$rows = [];
$err = null;
try {
    $rows = mypi_ledger_list([
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'kind' => 'guestcu',
        'tool' => 'cuBOOK',
        'order' => 'desc',
        'limit' => 80,
    ]);
} catch (Throwable $e) {
    $err = $e->getMessage();
}

$Parsedown = new Parsedown();
?>
<section class="cubook-list">
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
<?php elseif (!$rows): ?>
  <p class="muted">No guestbook entries yet (ledger).</p>
<?php else: ?>
  <?php foreach ($rows as $r):
      $unix = (int) ($r['event_unix'] ?: $r['ingest_unix']);
      $dt = (new DateTime('@' . $unix))->setTimezone(new DateTimeZone('America/New_York'));
      $agent = $r['agent'] !== '' ? $r['agent'] : 'anon';
      $body = $r['body'] ?? '';
  ?>
    <div class="soper_frag">
      <span class="userslug">User: <strong><?= htmlspecialchars($agent, ENT_QUOTES, 'UTF-8') ?></strong>
        on <?= htmlspecialchars($dt->format('m/d/y h:ia'), ENT_QUOTES, 'UTF-8') ?> says:</span>
      <span class="cuPOST"><?= $Parsedown->text($body) ?></span>
      <span class="userslug"><code><?= htmlspecialchars($r['c_uid'], ENT_QUOTES, 'UTF-8') ?></code></span>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</section>
