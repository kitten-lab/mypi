<?php
/**
 * fileKeeper · Desk — list heads · VIEW rendered markdown · EDIT to revise.
 * getTool('fileKeeper', 'Desk');
 *
 * Modes:
 *   new / edit=1  → editor form
 *   stem= / c=    → view (default) with "Modify" → editor
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$place = mypi_ledger_place_from_sky();
$stemQ = isset($_GET['stem']) ? trim((string) $_GET['stem']) : '';
$openQ = isset($_GET['c']) ? trim((string) $_GET['c']) : '';
$wantEdit = isset($_GET['edit']) || isset($_GET['new']);
$ok = isset($_GET['fk_ok']);

// default: brand-new desk with no selection → editor for a new file
$new = isset($_GET['new']) || ($stemQ === '' && $openQ === '' && !$ok);

$err = $GLOBALS['FILEKEEPER_ERROR'] ?? null;

$heads = [];
$folderNames = [];
try {
    $heads = mypi_ledger_file_heads([
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'limit' => 80,
    ]);
    $folderNames = mypi_ledger_file_folders([
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
    ]);
} catch (Throwable $e) {
    $err = $e->getMessage();
}

// group heads: '' = root
$byFolder = ['' => []];
foreach ($folderNames as $fn) {
    $byFolder[$fn] = [];
}
foreach ($heads as $h) {
    $hm = json_decode((string) ($h['meta_json'] ?? '{}'), true) ?: [];
    $f = mypi_ledger_file_folder_norm((string) ($hm['folder'] ?? ''));
    if (!isset($byFolder[$f])) {
        $byFolder[$f] = [];
    }
    $byFolder[$f][] = $h;
}

$title = '';
$body = '';
$parent = '';
$stem = '';
$rev = 1;
$revs = [];
$currentUid = '';
$eventDisplay = '';
$eventUnix = 0;
$tagsRaw = '';
$folder = isset($_GET['folder']) ? mypi_ledger_file_folder_norm((string) $_GET['folder']) : '';

if ($openQ !== '') {
    $row = mypi_ledger_get($openQ);
    if ($row && ($row['kind'] ?? '') === 'file') {
        $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
        $title = (string) ($row['topic'] ?? '');
        $body = (string) ($row['body'] ?? '');
        $stem = (string) ($meta['stem_c_uid'] ?? $row['c_uid']);
        $parent = (string) ($row['c_uid']);
        $rev = (int) ($meta['rev'] ?? 1);
        $currentUid = (string) $row['c_uid'];
        $tagsRaw = (string) ($row['tags_raw'] ?? '');
        $folder = mypi_ledger_file_folder_norm((string) ($meta['folder'] ?? ''));
        $eventUnix = (int) (!empty($row['event_unix']) ? $row['event_unix'] : ($row['ingest_unix'] ?? 0));
        if ($eventUnix > 0) {
            $eventDisplay = date('Y-m-d H:i:s', $eventUnix);
        }
        $new = false;
        $revs = mypi_ledger_file_revisions($stem);
    }
} elseif ($stemQ !== '' || $ok) {
    $stemKey = $stemQ !== '' ? $stemQ : trim((string) ($_GET['stem'] ?? ''));
    if ($stemKey === '' && !empty($_GET['stem'])) {
        $stemKey = trim((string) $_GET['stem']);
    }
    // after save redirect always has stem=
    if ($stemKey === '' && isset($_GET['stem'])) {
        $stemKey = (string) $_GET['stem'];
    }
    if ($stemKey !== '') {
        $revs = mypi_ledger_file_revisions($stemKey);
        if ($revs) {
            $row = $revs[count($revs) - 1];
            $meta = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
            $title = (string) ($row['topic'] ?? '');
            $body = (string) ($row['body'] ?? '');
            $stem = (string) ($meta['stem_c_uid'] ?? $stemKey);
            $parent = (string) ($row['c_uid']);
            $rev = (int) ($meta['rev'] ?? 1);
            $currentUid = (string) $row['c_uid'];
            $tagsRaw = (string) ($row['tags_raw'] ?? '');
            $folder = mypi_ledger_file_folder_norm((string) ($meta['folder'] ?? ''));
            $eventUnix = (int) (!empty($row['event_unix']) ? $row['event_unix'] : ($row['ingest_unix'] ?? 0));
            if ($eventUnix > 0) {
                $eventDisplay = date('Y-m-d H:i:s', $eventUnix);
            }
            $new = false;
        }
    }
}

if ($new) {
    $title = '';
    $body = '';
    $parent = '';
    $stem = '';
    $rev = 1;
    $currentUid = '';
    $eventDisplay = '';
    $eventUnix = 0;
    $tagsRaw = '';
    // keep ?folder= for new files dropped into a folder
    if (!isset($_GET['folder'])) {
        $folder = '';
    }
    $wantEdit = true;
}

// view vs edit: existing file opens in VIEW unless edit=1 or new
$mode = 'view';
if ($new || $wantEdit) {
    $mode = 'edit';
}
// after successful save, land in view
if ($ok && !$wantEdit && $stem !== '') {
    $mode = 'view';
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$editHref = $self . '?stem=' . rawurlencode($stem) . '&edit=1';
$viewHref = $self . '?stem=' . rawurlencode($stem);

// markdown render for view (GFM task lists + single-newline breaks for grids + media)
$rendered = '';
$mediaStrip = '';
$viewMeta = [];
if ($mode === 'view' && $currentUid !== '') {
    $crow = mypi_ledger_get($currentUid);
    $viewMeta = $crow ? (json_decode((string) ($crow['meta_json'] ?? '{}'), true) ?: []) : [];
    $prep = mypi_media_prepare_body_view($body, $viewMeta);
    $mediaStrip = $prep['html'];
    $bodyForMd = $prep['body'];
    $equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
    $pdTasks = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/ParsedownTasks.php';
    $pdBase = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php';
    if ($bodyForMd !== '') {
        if (is_file($equip)) {
            require_once $equip;
            $rendered = render_md($bodyForMd);
        } elseif (is_file($pdTasks)) {
            require_once $pdTasks;
            $pd = new ParsedownTasks();
            $pd->setSafeMode(true);
            $pd->setBreaksEnabled(true);
            $rendered = $pd->text($bodyForMd);
        } elseif (is_file($pdBase)) {
            require_once $pdBase;
            $pd = new Parsedown();
            $pd->setSafeMode(true);
            $pd->setBreaksEnabled(true);
            $rendered = $pd->text($bodyForMd);
        } else {
            $rendered = '<pre class="fk-pre">' . htmlspecialchars($bodyForMd, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
    }
    $rendered = $mediaStrip . $rendered;
}
?>
<div class="filekeeper">
  <div class="filekeeper-layout">
    <aside class="filekeeper-list" aria-label="File tree">
      <div class="fk-tree">
        <?php
        $renderFileLink = static function (array $h, string $stemActive, bool $isNew, string $selfPath): void {
            $hm = json_decode((string) ($h['meta_json'] ?? '{}'), true) ?: [];
            $hs = (string) ($hm['stem_c_uid'] ?? $h['c_uid']);
            $on = (!$isNew && $stemActive !== '' && $hs === $stemActive);
            $when = (int) (!empty($h['event_unix']) ? $h['event_unix'] : ($h['ingest_unix'] ?? 0));
            echo '<li>';
            echo '<a href="' . $selfPath . '?stem=' . rawurlencode($hs) . '" class="' . ($on ? 'is-on' : '') . '">';
            echo htmlspecialchars($h['topic'] !== '' ? $h['topic'] : 'untitled', ENT_QUOTES, 'UTF-8');
            echo '<span class="fk-meta">r' . (int) ($hm['rev'] ?? 1);
            echo $when ? ' · ' . date('m/d H:i', $when) : '';
            echo '</span></a></li>';
        };
        ?>

        <?php
        // Folders first, then root files
        foreach ($byFolder as $fname => $files):
            if ($fname === '') {
                continue;
            }
            $openFolder = ($folder === $fname) || (!$new && $stem !== '' && $folder === $fname);
            if (!$openFolder && $stem !== '') {
                foreach ($files as $fh) {
                    $fhm = json_decode((string) ($fh['meta_json'] ?? '{}'), true) ?: [];
                    if (($fhm['stem_c_uid'] ?? $fh['c_uid']) === $stem) {
                        $openFolder = true;
                        break;
                    }
                }
            }
            ?>
          <details class="fk-folder"<?= $openFolder ? ' open' : '' ?>>
            <summary class="fk-folder-sum">
              <span class="fk-folder-chev" aria-hidden="true"></span>
              <span class="fk-folder-name"><?= htmlspecialchars($fname, ENT_QUOTES, 'UTF-8') ?></span>
              <span class="fk-folder-n"><?= count($files) ?></span>
            </summary>
            <ul class="fk-file-ul fk-folder-files">
              <?php if ($files): ?>
                <?php foreach ($files as $h) {
                    $renderFileLink($h, $stem, $new, $self);
                } ?>
              <?php else: ?>
                <li class="fk-empty-folder">empty</li>
              <?php endif; ?>
              <li>
                <a href="<?= $self ?>?new=1&amp;folder=<?= rawurlencode($fname) ?>">+ file here</a>
              </li>
            </ul>
          </details>
        <?php endforeach; ?>

        <?php if (!empty($byFolder[''])): ?>
          <ul class="fk-file-ul fk-root-files">
            <?php foreach ($byFolder[''] as $h) {
                $renderFileLink($h, $stem, $new, $self);
            } ?>
          </ul>
        <?php endif; ?>

        <?php if (!$heads && !$folderNames): ?>
          <p class="fk-empty-hint">no files yet</p>
        <?php endif; ?>
      </div>

      <div class="fk-list-foot">
        <div class="fk-root-actions">
          <a class="fk-btn fk-btn-new<?= $new && $folder === '' ? ' is-on' : '' ?>"
             href="<?= $self ?>?new=1">+ New file</a>
        </div>
        <form method="post" class="fk-mkdir" action="">
          <input type="hidden" name="filekeeper_action" value="mkdir">
          <input type="hidden" name="fk_tz" value="">
          <label class="fk-mkdir-label" for="fk_mkdir">+ folder</label>
          <input id="fk_mkdir" name="fk_mkdir" type="text" placeholder="music · aleph-bet A-Z" required maxlength="80">
          <button type="submit">make</button>
        </form>
      </div>
    </aside>

    <section class="filekeeper-panel">
      <?php if ($err): ?>
        <p class="filekeeper-status" style="color:#f66"><strong>error:</strong> <?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif ($ok): ?>
        <p class="filekeeper-status">
          <strong>saved</strong>
          · rev <?= htmlspecialchars((string) ($_GET['rev'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
          <?php if (!empty($_GET['ev'])): ?>
            · event <?= htmlspecialchars(date('Y-m-d H:i:s', (int) $_GET['ev']), ENT_QUOTES, 'UTF-8') ?>
          <?php endif; ?>
        </p>
      <?php endif; ?>

      <?php if ($mode === 'view' && !$new && $currentUid !== ''): ?>
        <?php /* ── VIEW ─────────────────────────────────────── */ ?>
        <article class="filekeeper-view">
          <header class="fk-view-head">
            <h2 class="fk-view-title"><?= htmlspecialchars($title !== '' ? $title : 'untitled', ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="filekeeper-actions">
              <a class="fk-btn" href="<?= htmlspecialchars($editHref, ENT_QUOTES, 'UTF-8') ?>">Modify</a>
              <a class="fk-btn fk-btn-new" href="<?= $self ?>?new=1">+ New file</a>
            </div>
          </header>
          <p class="filekeeper-status">
            rev <?= (int) $rev ?>
            <?php if ($folder !== ''): ?>
              · <?= htmlspecialchars($folder, ENT_QUOTES, 'UTF-8') ?>
            <?php else: ?>
              · root
            <?php endif; ?>
            <?php if ($eventDisplay !== ''): ?>
              · <?= htmlspecialchars($eventDisplay, ENT_QUOTES, 'UTF-8') ?>
            <?php endif; ?>
          </p>
          <div class="fk-view-body">
            <?php if ($rendered !== ''): ?>
              <?= $rendered ?>
            <?php else: ?>
              <p class="muted"><em>(empty)</em></p>
            <?php endif; ?>
          </div>
          <?php if (!$new && $stem !== '' && $currentUid !== ''): ?>
            <form method="post" action="" enctype="multipart/form-data" class="fk-attach-form">
              <input type="hidden" name="filekeeper_action" value="attach">
              <input type="hidden" name="fk_stem" value="<?= htmlspecialchars($stem, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="fk_c_uid" value="<?= htmlspecialchars($currentUid, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="fk_tz" class="fk-tz" value="">
              <label class="fk-attach-label" for="fk_image">Attach image · IMG SUPPORT</label>
              <div class="fk-attach-row">
                <input id="fk_image" name="fk_image" type="file" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" required>
                <select name="fk_media_role" aria-label="role">
                  <option value="diagram">diagram</option>
                  <option value="cover">cover</option>
                  <option value="attach" selected>attach</option>
                </select>
                <button type="submit" class="fk-btn">Install image</button>
              </div>
              <p class="filekeeper-status muted">png/jpg/gif/webp · lands in d/_MEDIA · body can use <code>![](media:ID)</code></p>
            </form>
          <?php endif; ?>
          <?php if (count($revs) > 1): ?>
            <div class="filekeeper-revs">
              history:
              <?php foreach ($revs as $r):
                  $rm = json_decode((string) ($r['meta_json'] ?? '{}'), true) ?: [];
                  $rr = (int) ($rm['rev'] ?? 0);
                  $isHead = ($r['c_uid'] === $currentUid);
                  ?>
                <a href="<?= $self ?>?c=<?= rawurlencode($r['c_uid']) ?>"<?= $isHead ? ' class="is-on"' : '' ?>>r<?= $rr ?: '?' ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>

      <?php else: ?>
        <?php /* ── EDIT ─────────────────────────────────────── */ ?>
        <div class="filekeeper-editor">
          <?php if (!$new && $stem !== ''): ?>
            <p class="filekeeper-status">
              editing rev <?= (int) $rev ?> → save creates <strong>r<?= (int) $rev + 1 ?></strong>
              · <a href="<?= htmlspecialchars($viewHref, ENT_QUOTES, 'UTF-8') ?>">cancel · view</a>
            </p>
          <?php endif; ?>

          <form method="post" action="" class="filekeeper-form">
            <input type="hidden" name="filekeeper_action" value="save">
            <input type="hidden" name="fk_parent" value="<?= htmlspecialchars($parent, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="fk_stem" value="<?= htmlspecialchars($stem, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="fk_tz" id="fk-tz" value="">

            <label for="fk_title">Title</label>
            <input id="fk_title" name="fk_title" type="text" required
                   value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="name of this file">

            <label for="fk_folder">Folder <span style="opacity:0.55;text-transform:none;letter-spacing:0">(one layer · blank = root)</span></label>
            <select id="fk_folder" name="fk_folder" class="fk-folder-select">
              <option value=""<?= $folder === '' ? ' selected' : '' ?>>(root)</option>
              <?php foreach ($folderNames as $fn): ?>
                <option value="<?= htmlspecialchars($fn, ENT_QUOTES, 'UTF-8') ?>"<?= $folder === $fn ? ' selected' : '' ?>>
                  <?= htmlspecialchars($fn, ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
              <option value="__new__">+ new folder name…</option>
            </select>
            <input id="fk_folder_new" name="fk_folder_new" type="text" class="fk-folder-new"
                   placeholder="new folder name" style="display:none;margin-top:0.35rem" maxlength="80">

            <label for="fk_body">Body (markdown)</label>
            <textarea id="fk_body" name="fk_body" placeholder="write like the vault…"><?= htmlspecialchars($body, ENT_QUOTES, 'UTF-8') ?></textarea>

            <label for="fk_event">Event time <span style="opacity:0.55;text-transform:none;letter-spacing:0">(when it happened · any format)</span></label>
            <input id="fk_event" name="fk_event" type="text"
                   value="<?= htmlspecialchars($eventDisplay, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="now · 09/16/2025 10:16pm · September 16 2025 10:16PM"
                   autocomplete="off">
            <input type="hidden" name="fk_event_unix" id="fk-event-unix" value="">
            <p class="filekeeper-status fk-event-preview" id="fk-event-preview">event_unix: <em>now (on save)</em></p>

            <label for="fk_tags">Tags (optional · same submit · not lineage)</label>
            <input id="fk_tags" name="fk_tags" type="text"
                   placeholder="optional this*to>that"
                   value="<?= htmlspecialchars($tagsRaw, ENT_QUOTES, 'UTF-8') ?>">

            <div class="filekeeper-actions">
              <button type="submit"><?= $new ? 'Create file' : 'Save revision' ?></button>
              <?php if (!$new && $stem !== ''): ?>
                <a class="fk-btn fk-btn-quiet" href="<?= htmlspecialchars($viewHref, ENT_QUOTES, 'UTF-8') ?>">Cancel</a>
              <?php endif; ?>
              <a class="fk-btn fk-btn-new" href="<?= $self ?>?new=1">+ New file</a>
            </div>
          </form>
        </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<script>
(function () {
  var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
  document.querySelectorAll('#fk-tz, .fk-tz').forEach(function (el) { el.value = tz; });
})();
</script>
<?php if ($mode === 'edit'): ?>
<script>
(function () {
  var tzEl = document.getElementById('fk-tz');
  if (tzEl) tzEl.value = Intl.DateTimeFormat().resolvedOptions().timeZone;

  var input = document.getElementById('fk_event');
  var hidden = document.getElementById('fk-event-unix');
  var preview = document.getElementById('fk-event-preview');
  var form = document.querySelector('form.filekeeper-form');
  if (!input || !hidden || !preview) return;

  function scrub(s) {
    s = String(s || '').replace(/[\u200B-\u200F\u202A-\u202E\u2060-\u206F\uFEFF\u00AD]/g, '');
    s = s.replace(/[\u00A0\u202F\u2000-\u200A\u3000]/g, ' ');
    s = s.replace(/\b(monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun)\b[, ]*/gi, '');
    s = s.replace(/(\d)\s*([ap])\.?\s*m\.?/gi, '$1 $2m');
    s = s.replace(/\bat\b/gi, ' ');
    s = s.replace(/[,·—–]/g, ' ');
    s = s.replace(/\s+/g, ' ').trim();
    return s;
  }

  function parseLoose(s) {
    s = scrub(s);
    if (!s || /^(now|today|n)$/i.test(s)) return Math.floor(Date.now() / 1000);
    if (/^\d{9,13}$/.test(s)) {
      var n = parseInt(s, 10);
      if (n > 9999999999) n = Math.floor(n / 1000);
      return n;
    }
    var tryS = s.replace(/\b([ap])m\b/gi, function (_, a) {
      return a.toUpperCase() + 'M';
    });
    if (/^\d{4}-\d{2}-\d{2} \d{1,2}:\d{2}/.test(tryS)) {
      var msIso = Date.parse(tryS.replace(' ', 'T'));
      if (!isNaN(msIso)) return Math.floor(msIso / 1000);
    }
    var us = tryS.match(
      /^(\d{1,2})\/(\d{1,2})\/(\d{2,4})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?$/i
    );
    if (us) {
      var y = parseInt(us[3], 10);
      if (y < 100) y += 2000;
      var hh = parseInt(us[4], 10);
      var ap = (us[7] || '').toUpperCase();
      if (ap === 'PM' && hh < 12) hh += 12;
      if (ap === 'AM' && hh === 12) hh = 0;
      var dUs = new Date(y, parseInt(us[1], 10) - 1, parseInt(us[2], 10), hh, parseInt(us[5], 10), us[6] ? parseInt(us[6], 10) : 0);
      if (!isNaN(dUs.getTime())) return Math.floor(dUs.getTime() / 1000);
    }
    var mon = tryS.match(
      /^([A-Za-z]+)\s+(\d{1,2})\s+(\d{4})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?$/i
    );
    if (mon) {
      var dMon = new Date(
        mon[1] + ' ' + mon[2] + ', ' + mon[3] + ' ' + mon[4] + ':' + mon[5] +
        (mon[6] ? ':' + mon[6] : ':00') + (mon[7] ? ' ' + mon[7].toUpperCase() : '')
      );
      if (!isNaN(dMon.getTime())) return Math.floor(dMon.getTime() / 1000);
    }
    var ms = Date.parse(tryS);
    if (!isNaN(ms)) return Math.floor(ms / 1000);
    var d = new Date(tryS);
    if (!isNaN(d.getTime())) return Math.floor(d.getTime() / 1000);
    return null;
  }

  function paint() {
    var raw = input.value;
    if (!raw.trim()) {
      hidden.value = '';
      preview.innerHTML = 'event_unix: <em>now (on save)</em>';
      return;
    }
    var u = parseLoose(raw);
    if (u == null) {
      hidden.value = '';
      preview.innerHTML = 'event_unix: <em>…server will try harder</em>';
      return;
    }
    hidden.value = String(u);
    var d = new Date(u * 1000);
    preview.innerHTML =
      'event_unix: <strong>' + u + '</strong> · ' +
      d.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'medium' });
  }

  input.addEventListener('input', paint);
  input.addEventListener('change', paint);
  input.addEventListener('blur', paint);
  if (form) {
    form.addEventListener('submit', function () {
      paint();
      if (!input.value.trim()) {
        hidden.value = String(Math.floor(Date.now() / 1000));
      }
    });
  }
  paint();

  var foldSel = document.getElementById('fk_folder');
  var foldNew = document.getElementById('fk_folder_new');
  if (foldSel && foldNew) {
    function syncFolder() {
      var isNew = foldSel.value === '__new__';
      foldNew.style.display = isNew ? 'block' : 'none';
      if (isNew) foldNew.focus();
    }
    foldSel.addEventListener('change', syncFolder);
    syncFolder();
  }
})();
</script>
<?php endif; ?>
