<?php
/**
 * logImport · Active — list WIP imports; hop to START bay with face loaded.
 */
require_once __DIR__ . '/logImport_lib.php';

$wipList = logimport_list_wips();
$importHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'import')
    : '/terminal/io/import';
$h = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<div class="logimport">
  <p class="logimport-lede">
    active imports · wip wood only · glass sealed · complete exports hidden
  </p>
  <p class="logimport-meta">
    <a href="<?= $h($importHref) ?>">→ start an import</a>
    (load a core #)
  </p>

  <?php if (!$wipList): ?>
    <p class="logimport-warn">
      no active imports yet.
      <a href="<?= $h($importHref) ?>">start an import</a>
      · load core · cut / encode / note · save wip
    </p>
  <?php else: ?>
    <ul class="logimport-wip-ul">
      <?php foreach ($wipList as $w):
          $title = $w['yard_title'] !== ''
              ? $w['yard_title']
              : ($w['glass_title'] !== '' ? $w['glass_title'] : 'untitled');
          $href = $importHref . '?face=' . rawurlencode($w['face_id']);
          ?>
        <li>
          <a href="<?= $h($href) ?>">
            <strong><?= $h($w['face_id']) ?></strong>
            <?php if ($w['testament_tag'] !== ''): ?>
              <span class="logimport-meta">[<?= $h($w['testament_tag']) ?>]</span>
            <?php endif; ?>
            · <?= $h($title) ?>
            <?php if ($w['n_segments'] > 0): ?>
              <span class="logimport-meta"> · <?= (int) $w['n_segments'] ?> parts</span>
            <?php endif; ?>
          </a>
          <?php if ($w['saved_at']): ?>
            <span class="logimport-meta logimport-wip-when"><?= $h(date('m/d H:i', $w['saved_at'])) ?></span>
          <?php endif; ?>
          <?php if ($w['notes_preview'] !== ''): ?>
            <div class="logimport-meta logimport-wip-note"><?= $h($w['notes_preview']) ?></div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
