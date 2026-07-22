<?php
/**
 * logImport · Exports list — sealed materials after submit (stub until submit exists).
 * getTool('logImport', 'Exports');
 */
require_once __DIR__ . '/logImport_lib.php';

$exports = logimport_list_exports();
$importHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'import')
    : '/terminal/io/import';
?>
<div class="logimport">
  <p class="logimport-lede">
    exports · sealed wood · empty until you submit from import
  </p>

  <?php if (!$exports): ?>
    <p class="logimport-warn">
      no exports yet.
      work a core in
      <a href="<?= htmlspecialchars($importHref, ENT_QUOTES, 'UTF-8') ?>">import</a>
      (split · notes · save wip) — submit lands here later.
    </p>
    <p class="logimport-meta">
      staging path when live: <code>z/logs/tree_cores/exports/</code>
      · ledger materials still the real spine
    </p>
  <?php else: ?>
    <ul class="logimport-wip-ul">
      <?php foreach ($exports as $ex): ?>
        <li>
          <strong><?= htmlspecialchars($ex['face_id'] !== '' ? $ex['face_id'] : '—', ENT_QUOTES, 'UTF-8') ?></strong>
          · <?= htmlspecialchars($ex['title'], ENT_QUOTES, 'UTF-8') ?>
          <?php if ($ex['saved_at']): ?>
            <span class="logimport-meta"> · <?= htmlspecialchars(date('Y-m-d H:i', $ex['saved_at']), ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
