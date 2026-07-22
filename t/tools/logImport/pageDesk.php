<?php
/**
 * logImport · Desk — load tree-core by imposition number, view, WIP notes.
 * getTool('logImport', 'Desk');
 *
 * Glass shards are never modified. WIP → z/logs/tree_cores/wip/
 */
require_once __DIR__ . '/logImport_lib.php';

$faceQ = isset($_GET['face']) ? trim((string) $_GET['face']) : '';
$wipOk = isset($_GET['wip_ok']);
$err = $GLOBALS['LOGIMPORT_ERROR'] ?? null;

$catalog = logimport_load_catalog();
$core = $faceQ !== '' ? logimport_core_by_face($faceQ) : null;
$messages = [];
$wip = null;
$workingTitle = '';

if ($core) {
    $wip = logimport_wip_load((string) $core['face_id']);
    $workingTitle = is_array($wip) && ($wip['yard_title'] ?? '') !== ''
        ? (string) $wip['yard_title']
        : (string) ($core['title'] ?? '');
    $conv = logimport_load_conversation($core);
    if ($conv) {
        $messages = logimport_extract_messages($conv);
    } else {
        $err = $err ?: 'could not load conversation from shard (immutable glass missing?)';
    }
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$nCores = is_array($catalog) ? (int) ($catalog['n_cores'] ?? 0) : 0;
?>
<div class="logimport">
  <p class="logimport-lede">
    import · forest cores (OT+NT union) · glass sealed · wip only
  </p>

  <form class="logimport-load" method="get" action="<?= $self ?>">
    <label for="li_face">core #</label>
    <input id="li_face" name="face" type="text" inputmode="numeric" pattern="[0-9]*"
           value="<?= htmlspecialchars($faceQ !== '' ? logimport_face_key($faceQ) : '', ENT_QUOTES, 'UTF-8') ?>"
           placeholder="016" autocomplete="off">
    <button type="submit">load</button>
  </form>

  <?php if (!$catalog): ?>
    <p class="logimport-warn">
      no catalog. build with
      <code>python ledger/tree_core_catalog.py</code>
      (OT+NT → <code>z/logs/tree_cores/catalog.json</code> + <code>glass/</code> per-chat files).
    </p>
  <?php else: ?>
    <p class="logimport-meta">
      catalog · <strong><?= (int) $nCores ?></strong> cores · create_time order · OT+NT union
      <?php
      $st = is_array($catalog['stats'] ?? null) ? $catalog['stats'] : [];
      if ($st):
      ?>
        · OT-only <?= (int) ($st['n_ot_only'] ?? 0) ?>
        · NT-only <?= (int) ($st['n_nt_only'] ?? 0) ?>
        · both <?= (int) ($st['n_both'] ?? 0) ?>
      <?php endif; ?>
      <?php if (!empty($catalog['built_at'])): ?>
        · built <?= htmlspecialchars(date('Y-m-d H:i', (int) $catalog['built_at']), ENT_QUOTES, 'UTF-8') ?>
      <?php endif; ?>
    </p>
  <?php endif; ?>

  <?php if ($err): ?>
    <p class="logimport-status" style="opacity:1"><?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
  <?php elseif ($wipOk): ?>
    <p class="logimport-status">wip saved · glass untouched</p>
  <?php endif; ?>

  <?php if ($core): ?>
    <p class="logimport-meta">
      <strong><?= htmlspecialchars((string) $core['face_id'], ENT_QUOTES, 'UTF-8') ?></strong>
      · <span title="testament"><?= htmlspecialchars((string) ($core['testament_tag'] ?? '?'), ENT_QUOTES, 'UTF-8') ?></span>
      · load <?= htmlspecialchars((string) ($core['load_testament'] ?? $core['export_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
      · <?= htmlspecialchars((string) ($core['create_date_utc'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
      · <?= (int) ($core['message_count'] ?? 0) ?> msgs
      · <code><?= htmlspecialchars((string) ($core['conversation_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code>
      · glass <em><?= htmlspecialchars((string) ($core['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></em>
    </p>

    <form method="post" action="">
      <input type="hidden" name="logimport_action" value="save_wip">
      <input type="hidden" name="face" value="<?= htmlspecialchars((string) $core['face_id'], ENT_QUOTES, 'UTF-8') ?>">

      <label class="logimport-meta" for="li_title">working title (starts as glass)</label>
      <input class="logimport-title" id="li_title" name="yard_title" type="text"
             value="<?= htmlspecialchars($workingTitle, ENT_QUOTES, 'UTF-8') ?>">

      <div class="logimport-thread" id="li_thread">
        <?php if (!$messages): ?>
          <p class="logimport-meta">no user/assistant text extracted.</p>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <div class="logimport-msg role-<?= htmlspecialchars($m['role'], ENT_QUOTES, 'UTF-8') ?>">
              <div class="li-role"><?= htmlspecialchars($m['role'], ENT_QUOTES, 'UTF-8') ?> · #<?= (int) $m['seq'] ?></div>
              <pre class="li-body"><?= htmlspecialchars($m['text'], ENT_QUOTES, 'UTF-8') ?></pre>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <label class="logimport-meta" for="li_notes">notes (wip)</label>
      <textarea class="logimport-notes" id="li_notes" name="notes" placeholder="annotations for later encode / redact…"><?= htmlspecialchars((string) ($wip['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

      <div class="logimport-actions">
        <button type="submit">save wip</button>
        <span class="logimport-meta">encode · redact · split · submit — next</span>
      </div>
    </form>
  <?php elseif ($faceQ !== ''): ?>
    <p class="logimport-warn">no core for #<?= htmlspecialchars(logimport_face_key($faceQ), ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <p class="logimport-catalog-hint">
    prophetic load: type the number that found you · glass never rewritten
  </p>
</div>
