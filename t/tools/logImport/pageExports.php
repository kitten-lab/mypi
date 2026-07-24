<?php
/**
 * logImport · Exports list — sealed materials after submit_export.
 * getTool('logImport', 'Exports');
 * Groups by parent face; parts nested; complete + reopen log.
 * Glass never written.
 */
require_once __DIR__ . '/logImport_lib.php';

$groups = logimport_list_export_groups();
$importHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'import')
    : '/terminal/io/import';
$exportsHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'exports')
    : '/terminal/io/exports';
$exportOk = isset($_GET['export_ok']);
$completeOk = isset($_GET['complete_ok']);
$progressOk = isset($_GET['progress_ok']);
$partsN = isset($_GET['parts']) ? (int) $_GET['parts'] : 0;
$focusFace = isset($_GET['face']) ? logimport_face_key((string) $_GET['face']) : '';
$err = isset($GLOBALS['LOGIMPORT_ERROR']) ? (string) $GLOBALS['LOGIMPORT_ERROR'] : '';
$h = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$reopenMode = 'unlock';
?>
<div class="logimport">
  <p class="logimport-lede">
    exports · sealed wood · glass never written
  </p>

  <?php if ($err !== ''): ?>
    <p class="logimport-warn"><?= $h($err) ?></p>
  <?php elseif ($exportOk): ?>
    <p class="logimport-status">
      export sealed<?= $partsN > 1 ? ' · ' . (int) $partsN . ' parts' : '' ?>
      · status <strong>in progress</strong> until you mark complete
    </p>
  <?php elseif ($completeOk): ?>
    <p class="logimport-status">marked complete</p>
  <?php elseif ($progressOk): ?>
    <p class="logimport-status">back to in progress</p>
  <?php endif; ?>

  <?php if (!$groups): ?>
    <p class="logimport-warn">
      no exports yet.
      work a core in
      <a href="<?= $h($importHref) ?>">import</a>
      → save wip → <strong>seal export</strong>
    </p>
    <p class="logimport-meta">
      path: <code>z/logs/tree_cores/exports/export_{face}.json</code>
      · splits: <code>export_{face}.1.json</code> …
    </p>
  <?php else: ?>
    <ul class="logimport-export-groups">
      <?php foreach ($groups as $g):
          $isFocus = $focusFace !== '' && $focusFace === (string) $g['parent_face'];
          require __DIR__ . '/partExportCard.php';
      endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
