<?php
/**
 * logImport · Desk — load tree-core, view, notes, hand-split, encode, redact.
 * Glass never modified. WIP → z/logs/tree_cores/wip/
 * Phase 2 Woods: privatize before Log Yard.
 */
require_once __DIR__ . '/logImport_lib.php';

$faceQ = isset($_GET['face']) ? trim((string) $_GET['face']) : '';
$wipOk = isset($_GET['wip_ok']);
$splitOk = isset($_GET['split_ok']);
$unsplitOk = isset($_GET['unsplit_ok']);
$clearOk = isset($_GET['clear_ok']);
$encOk = isset($_GET['enc_ok']);
$encAlsoOk = isset($_GET['enc_also_ok']);
$encVenOk = isset($_GET['enc_ven_ok']);
$encRm = isset($_GET['enc_rm']);
$redOk = isset($_GET['red_ok']);
$redRm = isset($_GET['red_rm']);
$applyEnc = isset($_GET['apply_enc']);
$rawEnc = isset($_GET['raw_enc']);
$applyRed = isset($_GET['apply_red']);
$rawRed = isset($_GET['raw_red']);
$completeOk = isset($_GET['complete_ok']);
$progressOk = isset($_GET['progress_ok']);
$forceReopen = isset($_GET['reopen']);
$err = $GLOBALS['LOGIMPORT_ERROR'] ?? null;

$catalog = logimport_load_catalog();
$core = $faceQ !== '' ? logimport_core_by_face($faceQ) : null;
$messages = [];
$wip = null;
$workingTitle = '';
$segments = [];
$lastSeq = -1;
$cuts = [];
$seqToSeg = [];
$encodes = [];
$redactions = [];
$doEncode = false;
$doRedact = false;

// Complete export → gate desk unless ?reopen=1
$exportGate = false;
$exportGroup = null;
if ($core) {
    $faceKeyEarly = logimport_face_key((string) $core['face_id']);
    $exportSt = logimport_export_status_load($faceKeyEarly);
    if (($exportSt['status'] ?? '') === 'complete' && !$forceReopen) {
        $exportGate = true;
        $exportGroup = logimport_export_group_by_face($faceKeyEarly);
    }
}

if ($core && !$exportGate) {
    $wip = logimport_wip_load((string) $core['face_id']);
    $workingTitle = is_array($wip) && ($wip['yard_title'] ?? '') !== ''
        ? (string) $wip['yard_title']
        : (string) ($core['title'] ?? '');
    $conv = logimport_load_conversation($core);
    if ($conv) {
        $messages = logimport_extract_messages($conv);
    } else {
        $err = $err ?: 'could not load glass (run tree_core_catalog.py for glass/ extracts)';
    }
    if ($messages) {
        $lastSeq = (int) $messages[count($messages) - 1]['seq'];
        $segments = logimport_segments_normalize(
            is_array($wip) ? ($wip['segments'] ?? []) : [],
            $lastSeq
        );
        $cuts = logimport_segment_cuts(
            $segments !== []
                ? $segments
                : [['from_seq' => 0, 'to_seq' => $lastSeq, 'title' => '']]
        );
        $seqToSeg = logimport_seq_to_segment($segments, $lastSeq);
    }
    $encodes = logimport_encodes_list($wip);
    $redactions = logimport_redactions_list($wip);
    $flags = logimport_view_flags($wip);
    $doEncode = $flags['apply_encode'];
    $doRedact = $flags['apply_redact'];
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$nCores = is_array($catalog) ? (int) ($catalog['n_cores'] ?? 0) : 0;
$nSeg = count($segments);
$faceVal = $core ? (string) $core['face_id'] : '';
$wipList = logimport_list_wips();
$importHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'import')
    : '/terminal/io/import';
$exportsHref = function_exists('mypi_room_href')
    ? mypi_room_href('io', 'exports')
    : '/terminal/io/exports';
$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};

// message seqs that are wholly redacted (for button state)
$redMsgSeqs = [];
foreach ($redactions as $r) {
    if (($r['kind'] ?? '') === 'message') {
        $redMsgSeqs[(int) $r['seq']] = (string) $r['id'];
    }
}
?>
<div class="logimport">
  <p class="logimport-lede">
    start an import · enter core # · glass sealed · split · encode · redact
  </p>
  <?php
  $activeHref = function_exists('mypi_room_href')
      ? mypi_room_href('io', 'imports-active')
      : '/terminal/io/imports-active';
  ?>
  <p class="logimport-meta">
    wip list → <a href="<?= $h($activeHref) ?>">active imports</a>
    <?php if ($wipList): ?>
      · <?= count($wipList) ?> active
    <?php endif; ?>
  </p>

  <form class="logimport-load" method="get" action="<?= $self ?>">
    <label for="li_face">core #</label>
    <input id="li_face" name="face" type="text" inputmode="numeric" pattern="[0-9]*"
           value="<?= $h($faceQ !== '' ? logimport_face_key($faceQ) : '') ?>"
           placeholder="100" autocomplete="off">
    <button type="submit">load</button>
  </form>

  <?php if (!$catalog): ?>
    <p class="logimport-warn">
      no catalog. <code>python ledger/tree_core_catalog.py</code>
    </p>
  <?php else: ?>
    <p class="logimport-meta">
      catalog · <strong><?= (int) $nCores ?></strong> cores · OT+NT union
      <?php
      $st = is_array($catalog['stats'] ?? null) ? $catalog['stats'] : [];
      if ($st):
      ?>
        · OT <?= (int) ($st['n_ot_only'] ?? 0) ?>
        · NT <?= (int) ($st['n_nt_only'] ?? 0) ?>
        · both <?= (int) ($st['n_both'] ?? 0) ?>
      <?php endif; ?>
    </p>
  <?php endif; ?>

  <?php if ($err): ?>
    <p class="logimport-status logimport-err"><?= $h((string) $err) ?></p>
  <?php elseif ($completeOk): ?>
    <p class="logimport-status">marked complete · WIP closed until reopen</p>
  <?php elseif ($progressOk): ?>
    <p class="logimport-status">back in progress · active list again · desk open</p>
  <?php elseif ($splitOk): ?>
    <p class="logimport-status">cut placed · <?= (int) max(1, $nSeg) ?> segment(s) · names kept</p>
  <?php elseif ($unsplitOk): ?>
    <p class="logimport-status">cut removed</p>
  <?php elseif ($clearOk): ?>
    <p class="logimport-status">all cuts cleared · whole log again</p>
  <?php elseif ($encOk): ?>
    <p class="logimport-status">encode filed · original stays in z/ wip only · match is case-insensitive</p>
  <?php elseif ($encAlsoOk): ?>
    <p class="logimport-status">also-spellings added · no need to delete the row</p>
  <?php elseif ($encVenOk): ?>
    <p class="logimport-status">pushed to RX venDesk · z/ven_registry · matches = original + also</p>
  <?php elseif ($encRm): ?>
    <p class="logimport-status">encode removed from book</p>
  <?php elseif ($redOk): ?>
    <p class="logimport-status">redaction filed · glass still whole</p>
  <?php elseif ($redRm): ?>
    <p class="logimport-status">redaction removed</p>
  <?php elseif ($applyEnc): ?>
    <p class="logimport-status">aliases <strong>applied</strong> in view (EDN-style) · glass raw under it</p>
  <?php elseif ($rawEnc): ?>
    <p class="logimport-status">aliases off · showing glass originals</p>
  <?php elseif ($applyRed): ?>
    <p class="logimport-status">redactions <strong>applied</strong> in view · bars only</p>
  <?php elseif ($rawRed): ?>
    <p class="logimport-status">redactions off · showing unbarred glass</p>
  <?php elseif ($wipOk): ?>
    <p class="logimport-status">wip saved · glass untouched</p>
  <?php endif; ?>

  <?php if ($core && $exportGate): ?>
    <div class="logimport-already">
      <h2 class="logimport-already-h">Already exported!</h2>
      <p class="logimport-meta">
        core <strong><?= $h($faceVal) ?></strong> is marked complete.
        WIP and glass stay sealed until you <strong>reopen log</strong>
        (peek/edit) or <strong>mark in progress</strong> (back on active list).
      </p>
      <ul class="logimport-export-groups">
        <?php
        $g = is_array($exportGroup) ? $exportGroup : [
            'parent_face' => $faceVal,
            'glass_title' => (string) ($core['title'] ?? ''),
            'yard_title' => '',
            'display_title' => (string) ($core['title'] ?? 'untitled'),
            'part_count' => 0,
            'exported_at' => 0,
            'status' => 'complete',
            'completed_at' => null,
            'parts' => [],
        ];
        $isFocus = true;
        $reopenMode = 'unlock';
        $returnTo = 'import';
        require __DIR__ . '/partExportCard.php';
        ?>
      </ul>
      <p class="logimport-meta">
        <a href="<?= $h($exportsHref . '?face=' . rawurlencode($faceVal)) ?>">→ all exports</a>
      </p>
    </div>

  <?php elseif ($core): ?>
    <?php if ($forceReopen): ?>
      <p class="logimport-status logimport-reopen-banner">
        reopened WIP
        <?php
        $stNow = logimport_export_status_load($faceVal);
        if (($stNow['status'] ?? '') === 'complete'):
        ?>
          · export still <strong>complete</strong> (hidden from active)
          · <a href="<?= $h($importHref . '?face=' . rawurlencode($faceVal)) ?>">back to export summary</a>
        <?php endif; ?>
      </p>
    <?php endif; ?>
    <p class="logimport-meta">
      <strong><?= $h($faceVal) ?></strong>
      · <?= $h((string) ($core['testament_tag'] ?? '?')) ?>
      · <?= $h((string) ($core['create_date_utc'] ?? '')) ?>
      · <?= count($messages) ?> msgs
      <?php if ($nSeg > 0): ?>
        · <strong><?= (int) $nSeg ?> parts</strong>
      <?php endif; ?>
      · enc <?= count($encodes) ?><?= $doEncode ? ' · ON' : '' ?>
      · red <?= count($redactions) ?><?= $doRedact ? ' · ON' : '' ?>
      · glass <em><?= $h((string) ($core['title'] ?? '')) ?></em>
    </p>

    <form method="post" action="" id="li_main">
      <input type="hidden" name="face" value="<?= $h($faceVal) ?>">

      <label class="logimport-meta" for="li_title">working title (starts as glass)</label>
      <input class="logimport-title" id="li_title" name="yard_title" type="text"
             value="<?= $h($workingTitle) ?>">

      <?php /* ── Phase 2: encode book ── */ ?>
      <section class="logimport-panel" id="li-encode">
        <h3 class="logimport-subh">Encode book · privatize names</h3>
        <p class="logimport-meta">
          original stays in <code>z/…/wip</code> only · alias is the public face · apply is a button (not silent)
        </p>
        <div class="logimport-enc-form">
          <input type="text" name="enc_original" placeholder="original (private)" autocomplete="off">
          <input type="text" name="enc_alias" placeholder="alias (public)" autocomplete="off">
          <input type="text" name="enc_code" placeholder="code (auto if blank)" autocomplete="off" class="logimport-code">
          <input type="text" name="enc_also" placeholder="also spelled (comma-sep, optional)" autocomplete="off" class="logimport-also">
          <button type="submit" name="add_encode" value="1">+ encode</button>
        </div>
        <p class="logimport-meta">
          <strong>also spelled</strong> = extra log spellings that map to the same alias
          (like VEN matches on RX). e.g. original Sam King · also Sam, Sammy, SK
        </p>
        <?php if ($encodes): ?>
          <ul class="logimport-book">
            <?php foreach ($encodes as $e):
                $also = is_array($e['also'] ?? null) ? $e['also'] : [];
                $eid = (string) $e['id'];
                $eidH = $h($eid);
                $alsoKey = logimport_encode_form_key($eid);
                ?>
              <li class="logimport-enc-row">
                <div class="logimport-enc-main">
                  <code class="li-code"><?= $h($e['code']) ?></code>
                  <span class="li-alias"><?= $h($e['alias']) ?></span>
                  <span class="li-arrow">←</span>
                  <span class="li-orig muted"><?= $h($e['original']) ?></span>
                  <?php if ($also): ?>
                    <span class="logimport-meta"> · also <?= $h(implode(', ', $also)) ?></span>
                  <?php endif; ?>
                </div>
                <div class="logimport-enc-actions">
                  <input type="text" name="enc_also_<?= $h($alsoKey) ?>"
                         placeholder="+ also spelled…" class="logimport-also-inline" autocomplete="off">
                  <button type="submit" name="add_encode_also" value="<?= $eidH ?>">add also</button>
                  <button type="submit" name="push_encode_ven" value="<?= $eidH ?>" title="write to RX venDesk registry">→ VEN</button>
                  <button type="submit" name="remove_encode" value="<?= $eidH ?>" class="logimport-cut">remove</button>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="logimport-meta">no encodes yet · e.g. real name → Haji · code HJI-001 · also spellings</p>
        <?php endif; ?>
        <div class="logimport-apply-row">
          <?php if ($doEncode): ?>
            <button type="submit" name="clear_apply_encode" value="1" class="logimport-cut is-cut">show glass names</button>
            <span class="logimport-meta">viewing aliases</span>
          <?php else: ?>
            <button type="submit" name="apply_encode" value="1"<?= $encodes ? '' : ' disabled' ?>>Apply aliases</button>
            <span class="logimport-meta">viewing originals</span>
          <?php endif; ?>
        </div>
      </section>

      <?php /* ── Phase 2: redact ── */ ?>
      <section class="logimport-panel" id="li-redact">
        <h3 class="logimport-subh">Redact · black bars</h3>
        <p class="logimport-meta">
          hide content (not rename) · phrase anywhere in thread · or whole message via button on the msg
        </p>
        <div class="logimport-enc-form">
          <input type="text" name="red_original" placeholder="phrase to bar out" autocomplete="off">
          <input type="text" name="red_label" placeholder="label (optional)" autocomplete="off" class="logimport-code">
          <button type="submit" name="add_redact_phrase" value="1">+ redact phrase</button>
        </div>
        <?php if ($redactions): ?>
          <ul class="logimport-book">
            <?php foreach ($redactions as $r): ?>
              <li>
                <?php if (($r['kind'] ?? '') === 'message'): ?>
                  <code class="li-code">MSG</code>
                  <span class="li-alias">#<?= (int) $r['seq'] ?></span>
                  <span class="muted">whole message</span>
                <?php else: ?>
                  <code class="li-code">█</code>
                  <span class="li-orig"><?= $h((string) ($r['original'] ?? '')) ?></span>
                  <?php if (!empty($r['label'])): ?>
                    <span class="muted">(<?= $h((string) $r['label']) ?>)</span>
                  <?php endif; ?>
                <?php endif; ?>
                <button type="submit" name="remove_redact" value="<?= $h($r['id']) ?>" class="logimport-cut">remove</button>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="logimport-meta">no redactions yet</p>
        <?php endif; ?>
        <div class="logimport-apply-row">
          <?php if ($doRedact): ?>
            <button type="submit" name="clear_apply_redact" value="1" class="logimport-cut is-cut">show unbarred</button>
            <span class="logimport-meta">viewing bars</span>
          <?php else: ?>
            <button type="submit" name="apply_redact" value="1"<?= $redactions ? '' : ' disabled' ?>>Apply redactions</button>
            <span class="logimport-meta">viewing raw glass text</span>
          <?php endif; ?>
        </div>
      </section>

      <div class="logimport-thread" id="li_thread">
        <?php if (!$messages): ?>
          <p class="logimport-meta">no user/assistant text extracted.</p>
        <?php else: ?>
          <?php
          $prevSeg = -1;
          foreach ($messages as $m):
              $seq = (int) $m['seq'];
              $si = $seqToSeg[$seq] ?? 0;
              if ($nSeg > 0 && $si !== $prevSeg):
                  $prevSeg = $si;
                  $segTitle = trim((string) ($segments[$si]['title'] ?? ''));
                  ?>
            <div class="logimport-seg-head" id="li-seghead-<?= (int) $si ?>">
              <span class="logimport-seg-scissors" aria-hidden="true">✂</span>
              <input type="text"
                     name="seg_title[<?= (int) $si ?>]"
                     class="logimport-seg-title"
                     id="li-seg-<?= (int) $si ?>"
                     value="<?= $h($segTitle) ?>"
                     placeholder="name this part"
                     autocomplete="off">
              <span class="logimport-seg-range">#<?= (int) $segments[$si]['from_seq'] ?>–#<?= (int) $segments[$si]['to_seq'] ?></span>
            </div>
                  <?php
              endif;
              $tx = logimport_transform_text((string) $m['text'], $seq, $wip, $doRedact, $doEncode, true);
              $cls = 'logimport-msg role-' . $h($m['role']);
              if (!empty($tx['encode_marked'])) {
                  $cls .= ' has-enc';
              }
              if (!empty($tx['wholly_redacted'])) {
                  $cls .= ' is-redacted';
              }
              ?>
            <div class="<?= $cls ?>" id="li-msg-<?= $seq ?>">
              <div class="li-role">
                <?= $h($m['role']) ?> · #<?= $seq ?>
                <?php if (!empty($tx['hit_count'])): ?>
                  <span class="li-enc-count"><?= (int) $tx['hit_count'] ?> enc</span>
                <?php endif; ?>
              </div>
              <?php if (!empty($tx['encode_marked'])): ?>
                <pre class="li-body li-body-enc"><?= logimport_format_marked_html((string) $tx['text']) ?></pre>
              <?php else: ?>
                <pre class="li-body"><?= $h($tx['text']) ?></pre>
              <?php endif; ?>
              <div class="logimport-cut-row">
                <?php if (isset($redMsgSeqs[$seq])): ?>
                  <button type="submit" name="remove_redact" value="<?= $h($redMsgSeqs[$seq]) ?>" class="logimport-cut is-cut">
                    unredact msg #<?= $seq ?>
                  </button>
                <?php else: ?>
                  <button type="submit" name="add_redact_msg" value="<?= $seq ?>" class="logimport-cut">
                    redact msg #<?= $seq ?>
                  </button>
                <?php endif; ?>
                <?php if ($seq < $lastSeq): ?>
                  <?php if (in_array($seq, $cuts, true)): ?>
                    <button type="submit" name="unsplit_after" value="<?= $seq ?>" class="logimport-cut is-cut">
                      unsplit after #<?= $seq ?>
                    </button>
                  <?php else: ?>
                    <button type="submit" name="split_after" value="<?= $seq ?>" class="logimport-cut">
                      split after #<?= $seq ?>
                    </button>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <label class="logimport-meta" for="li_notes">notes (wip · rides export later)</label>
      <textarea class="logimport-notes" id="li_notes" name="notes" placeholder="lumberjack notes…"><?= $h((string) ($wip['notes'] ?? '')) ?></textarea>

      <div class="logimport-actions">
        <button type="submit" name="logimport_action" value="save_wip">save wip</button>
        <?php if ($nSeg > 0): ?>
          <button type="submit" name="clear_splits" value="1" class="logimport-cut">clear all cuts</button>
        <?php endif; ?>
        <button type="submit" name="submit_export" value="1" class="logimport-export">
          seal export<?= $nSeg > 1 ? ' · ' . (int) $nSeg . ' parts' : '' ?>
        </button>
        <span class="logimport-meta">→ <code>z/logs/tree_cores/exports/</code> · glass sealed</span>
      </div>
    </form>

  <?php elseif ($faceQ !== ''): ?>
    <p class="logimport-warn">no core for #<?= $h(logimport_face_key($faceQ)) ?></p>
  <?php endif; ?>

  <p class="logimport-catalog-hint">
    encode · redact · apply by button · glass sealed · seal export writes exports/ only
  </p>
</div>
<?php if ($core && $messages): ?>
<script>
(function () {
  if (location.hash && location.hash.indexOf('li-msg-') === 1) {
    var el = document.getElementById(location.hash.slice(1));
    if (el) {
      var thread = document.getElementById('li_thread');
      if (thread && thread.contains(el)) {
        var top = el.offsetTop - thread.offsetTop - 24;
        thread.scrollTop = Math.max(0, top);
      } else {
        el.scrollIntoView({ block: 'center' });
      }
    }
  }
  if (location.hash === '#li-encode' || location.hash === '#li-redact') {
    var p = document.getElementById(location.hash.slice(1));
    if (p) p.scrollIntoView({ block: 'nearest' });
  }
})();
</script>
<?php endif; ?>
