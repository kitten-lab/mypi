<?php
/**
 * logImport · Desk — load tree-core, view, notes, hand-split segments.
 * One form for titles + cuts so names survive every split.
 * Glass never modified. WIP → z/logs/tree_cores/wip/
 */
require_once __DIR__ . '/logImport_lib.php';

$faceQ = isset($_GET['face']) ? trim((string) $_GET['face']) : '';
$wipOk = isset($_GET['wip_ok']);
$splitOk = isset($_GET['split_ok']);
$unsplitOk = isset($_GET['unsplit_ok']);
$clearOk = isset($_GET['clear_ok']);
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

if ($core) {
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
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$nCores = is_array($catalog) ? (int) ($catalog['n_cores'] ?? 0) : 0;
$nSeg = count($segments);
$faceVal = $core ? (string) $core['face_id'] : '';
$wipList = logimport_list_wips();
?>
<div class="logimport">
  <p class="logimport-lede">
    import · forest cores · glass sealed · split the log · wip only
  </p>

  <form class="logimport-load" method="get" action="<?= $self ?>">
    <label for="li_face">core #</label>
    <input id="li_face" name="face" type="text" inputmode="numeric" pattern="[0-9]*"
           value="<?= htmlspecialchars($faceQ !== '' ? logimport_face_key($faceQ) : '', ENT_QUOTES, 'UTF-8') ?>"
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

  <?php /* Always show WIPs on the import desk (any cores you've touched) */ ?>
  <section class="logimport-wips" id="li-wips">
    <h3 class="logimport-subh">WIPs</h3>
    <?php if (!$wipList): ?>
      <p class="logimport-meta">none yet · load a core, cut or note, save wip</p>
    <?php else: ?>
      <ul class="logimport-wip-ul">
        <?php foreach ($wipList as $w):
            $title = $w['yard_title'] !== '' ? $w['yard_title'] : ($w['glass_title'] !== '' ? $w['glass_title'] : 'untitled');
            $href = $self . '?face=' . rawurlencode($w['face_id']);
            $on = ($faceVal !== '' && $faceVal === $w['face_id']);
            ?>
          <li class="<?= $on ? 'is-on' : '' ?>">
            <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
              <strong><?= htmlspecialchars($w['face_id'], ENT_QUOTES, 'UTF-8') ?></strong>
              <?php if ($w['testament_tag'] !== ''): ?>
                <span class="logimport-meta">[<?= htmlspecialchars($w['testament_tag'], ENT_QUOTES, 'UTF-8') ?>]</span>
              <?php endif; ?>
              · <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
              <?php if ($w['n_segments'] > 0): ?>
                <span class="logimport-meta"> · <?= (int) $w['n_segments'] ?> parts</span>
              <?php endif; ?>
            </a>
            <?php if ($w['saved_at']): ?>
              <span class="logimport-meta logimport-wip-when"><?= htmlspecialchars(date('m/d H:i', $w['saved_at']), ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <?php if ($w['notes_preview'] !== ''): ?>
              <div class="logimport-meta logimport-wip-note"><?= htmlspecialchars($w['notes_preview'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <?php if ($err): ?>
    <p class="logimport-status" style="opacity:1"><?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
  <?php elseif ($splitOk): ?>
    <p class="logimport-status">cut placed · <?= (int) max(1, $nSeg) ?> segment(s) · names kept</p>
  <?php elseif ($unsplitOk): ?>
    <p class="logimport-status">cut removed</p>
  <?php elseif ($clearOk): ?>
    <p class="logimport-status">all cuts cleared · whole log again</p>
  <?php elseif ($wipOk): ?>
    <p class="logimport-status">wip saved · glass untouched</p>
  <?php endif; ?>

  <?php if ($core): ?>
    <p class="logimport-meta">
      <strong><?= htmlspecialchars($faceVal, ENT_QUOTES, 'UTF-8') ?></strong>
      · <?= htmlspecialchars((string) ($core['testament_tag'] ?? '?'), ENT_QUOTES, 'UTF-8') ?>
      · <?= htmlspecialchars((string) ($core['create_date_utc'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
      · <?= count($messages) ?> msgs
      <?php if ($nSeg > 0): ?>
        · <strong><?= (int) $nSeg ?> parts</strong>
      <?php endif; ?>
      · glass <em><?= htmlspecialchars((string) ($core['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></em>
    </p>

    <?php /* ONE form: titles + notes + split/unsplit buttons (names survive cuts) */ ?>
    <form method="post" action="" id="li_main">
      <input type="hidden" name="face" value="<?= htmlspecialchars($faceVal, ENT_QUOTES, 'UTF-8') ?>">

      <label class="logimport-meta" for="li_title">working title (starts as glass)</label>
      <input class="logimport-title" id="li_title" name="yard_title" type="text"
             value="<?= htmlspecialchars($workingTitle, ENT_QUOTES, 'UTF-8') ?>">

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
                     value="<?= htmlspecialchars($segTitle, ENT_QUOTES, 'UTF-8') ?>"
                     placeholder="name this part"
                     autocomplete="off">
              <span class="logimport-seg-range">#<?= (int) $segments[$si]['from_seq'] ?>–#<?= (int) $segments[$si]['to_seq'] ?></span>
            </div>
                  <?php
              endif;
              ?>
            <div class="logimport-msg role-<?= htmlspecialchars($m['role'], ENT_QUOTES, 'UTF-8') ?>"
                 id="li-msg-<?= $seq ?>">
              <div class="li-role">
                <?= htmlspecialchars($m['role'], ENT_QUOTES, 'UTF-8') ?> · #<?= $seq ?>
              </div>
              <pre class="li-body"><?= htmlspecialchars($m['text'], ENT_QUOTES, 'UTF-8') ?></pre>
              <?php if ($seq < $lastSeq): ?>
                <div class="logimport-cut-row">
                  <?php if (in_array($seq, $cuts, true)): ?>
                    <button type="submit" name="unsplit_after" value="<?= $seq ?>" class="logimport-cut is-cut">
                      unsplit after #<?= $seq ?>
                    </button>
                  <?php else: ?>
                    <button type="submit" name="split_after" value="<?= $seq ?>" class="logimport-cut">
                      split after #<?= $seq ?>
                    </button>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <label class="logimport-meta" for="li_notes">notes (wip · rides export later)</label>
      <textarea class="logimport-notes" id="li_notes" name="notes" placeholder="lumberjack notes…"><?= htmlspecialchars((string) ($wip['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

      <div class="logimport-actions">
        <button type="submit" name="logimport_action" value="save_wip">save wip</button>
        <?php if ($nSeg > 0): ?>
          <button type="submit" name="clear_splits" value="1" class="logimport-cut">clear all cuts</button>
        <?php endif; ?>
        <span class="logimport-meta">encode · redact · submit — later</span>
      </div>
    </form>

  <?php elseif ($faceQ !== ''): ?>
    <p class="logimport-warn">no core for #<?= htmlspecialchars(logimport_face_key($faceQ), ENT_QUOTES, 'UTF-8') ?></p>
  <?php endif; ?>

  <p class="logimport-catalog-hint">
    lumberjack: split after a message · name the parts · save wip · glass stays whole
  </p>
</div>
<?php if ($core && $messages): ?>
<script>
(function () {
  // Keep scroll position after redirect to #li-msg-N (browser may run before layout)
  if (location.hash && location.hash.indexOf('li-msg-') === 1) {
    var el = document.getElementById(location.hash.slice(1));
    if (el) {
      // thread is the scrollport — scroll message into view inside it
      var thread = document.getElementById('li_thread');
      if (thread && thread.contains(el)) {
        var top = el.offsetTop - thread.offsetTop - 24;
        thread.scrollTop = Math.max(0, top);
      } else {
        el.scrollIntoView({ block: 'center' });
      }
    }
  }
})();
</script>
<?php endif; ?>
