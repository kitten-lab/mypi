<?php
/**
 * sopr fragments for this place, grouped by section (topic).
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
        'kind' => 'soper',
        'tool' => 'soprBASIC',
        'order' => 'desc',
        'limit' => 120,
    ]);
} catch (Throwable $e) {
    $err = $e->getMessage();
}

$Parsedown = new Parsedown();
$Parsedown->setSafeMode(true);
$Parsedown->setBreaksEnabled(true);
$bySection = [];
foreach ($rows as $r) {
    $meta = json_decode($r['meta_json'] ?? '{}', true) ?: [];
    $sec = $meta['section'] ?? ($r['topic'] !== '' ? $r['topic'] : 'loose');
    $bySection[$sec][] = $r;
}
?>
<section class="sopr-list">
<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
<?php elseif (!$bySection): ?>
  <p class="muted">No fragments yet (ledger).</p>
<?php else: ?>
  <?php foreach ($bySection as $label => $items): ?>
    <h3><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></h3>
    <?php foreach ($items as $SOPR):
        $unix = (int) ($SOPR['event_unix'] ?: $SOPR['ingest_unix']);
        $when = date('m/d/y H:i', $unix);
    ?>
      <div class="soper_frag">
        <div class="slug"><?= htmlspecialchars($SOPR['c_uid'], ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars($when, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="content"><?= $Parsedown->text($SOPR['body'] ?? '') ?></div>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
<?php endif; ?>
</section>
