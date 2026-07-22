<?php
/**
 * inventOry · invent-0rium desk
 * Day log + leaf inserts; optional dual-write to Skyline service buckets.
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$place = mypi_ledger_place_from_sky();
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'io';
$room = 'inventory';
$agentSlug = defined('MOD_SLUG') ? MOD_SLUG : (defined('MOD_DISPLAY') ? MOD_DISPLAY : 'user');

$today = date('Y-m-d');
$day = isset($_GET['day']) ? mypi_ledger_dailylog_day_norm((string) $_GET['day']) : $today;
if ($day === '') {
    $day = $today;
}

$err = $GLOBALS['INVENTORY_ERROR'] ?? (isset($_GET['inv_err']) ? (string) $_GET['inv_err'] : null);
$ok = isset($_GET['inv_ok']);
$rpt = isset($_GET['rpt']) ? (string) $_GET['rpt'] : '';
$whom = isset($_GET['whom']) ? (string) $_GET['whom'] : '';
$importFlash = null;
if (isset($_GET['inv_import'])) {
    $flash = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'inventOry_import_' . md5($agentSlug) . '.json';
    if (is_file($flash)) {
        $importFlash = json_decode((string) file_get_contents($flash), true);
    }
}

// Load day if it exists — do NOT auto-spawn a shell just by viewing a date.
// New shells are created on insert or vault import only.
$dayRow = mypi_ledger_dailylog_find_day($day, $sys, $dom, $room, $agentSlug);
$dayUid = $dayRow ? (string) $dayRow['c_uid'] : '';
$dayMeta = $dayRow ? (json_decode((string) ($dayRow['meta_json'] ?? '{}'), true) ?: []) : [];
$closed = !empty($dayMeta['closed']);
$dayExists = $dayUid !== '';

$days = mypi_ledger_dailylog_list_days([
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'agent' => $agentSlug,
    'limit' => 90,
]);
$entries = $dayUid !== '' ? mypi_ledger_dailylog_list_entries($dayUid, 300) : [];
$buckets = mypi_ledger_report_buckets();

// group leaves by section
$bySection = [];
foreach ($entries as $e) {
    $em = json_decode((string) ($e['meta_json'] ?? '{}'), true) ?: [];
    $sec = (string) ($em['section'] ?? 'INCOMING EVENTS');
    if ($sec === '') {
        $sec = 'INCOMING EVENTS';
    }
    $bySection[$sec][] = $e;
}
$sectionOrder = $dayMeta['sections'] ?? array_keys($bySection);
if (!is_array($sectionOrder) || !$sectionOrder) {
    $sectionOrder = array_keys($bySection);
}
foreach (array_keys($bySection) as $s) {
    if (!in_array($s, $sectionOrder, true)) {
        $sectionOrder[] = $s;
    }
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');

$renderEntry = static function (array $e) use ($equip): void {
    $em = json_decode((string) ($e['meta_json'] ?? '{}'), true) ?: [];
    $wall = (string) ($em['wall_time'] ?? '');
    $ctx = (string) ($em['context'] ?? '');
    $when = (int) ($e['event_unix'] ?? 0);
    $title = (string) ($e['topic'] ?? '');
    $body = (string) ($e['body'] ?? '');
    // strip trailing CONTEXT block from display body if we show it separately
    if ($ctx !== '' && $body !== '') {
        $body = preg_replace('/\n\nCONTEXT:\s*\*\*.*?\*\*\s*$/s', '', $body) ?? $body;
    }
    echo '<article class="inv-leaf" id="e-' . htmlspecialchars($e['c_uid'], ENT_QUOTES, 'UTF-8') . '">';
    echo '<header class="inv-leaf-head">';
    echo '<h4 class="inv-leaf-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h4>';
    echo '<span class="inv-leaf-when">';
    if ($wall !== '') {
        echo htmlspecialchars($wall, ENT_QUOTES, 'UTF-8');
    } elseif ($when > 0) {
        echo htmlspecialchars(date('g:i A', $when), ENT_QUOTES, 'UTF-8');
    }
    echo '</span></header>';
    if ($body !== '') {
        echo '<div class="inv-leaf-body">';
        if (function_exists('render_md')) {
            echo render_md($body);
        } else {
            echo nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
        }
        echo '</div>';
    }
    if ($ctx !== '') {
        echo '<p class="inv-leaf-ctx"><span class="inv-k">CONTEXT</span> '
            . htmlspecialchars($ctx, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    $tags = (string) ($e['tags_raw'] ?? '');
    if ($tags !== '') {
        echo '<p class="inv-leaf-tags"><span class="inv-k">TAGGED</span> '
            . htmlspecialchars($tags, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '<p class="inv-leaf-meta muted">'
        . htmlspecialchars($e['c_uid'], ENT_QUOTES, 'UTF-8');
    if ($when > 0) {
        echo ' · ' . htmlspecialchars(date('Y-m-d H:i', $when), ENT_QUOTES, 'UTF-8');
    }
    echo '</p></article>';
};
?>
<div class="inventury" id="inventury-desk">
  <div class="inventury-layout">
    <aside class="inventury-days" aria-label="Day logs">
      <a class="inv-btn inv-btn-primary" href="<?= $self ?>?day=<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>#inv-insert">+ Insert today</a>
      <form class="inv-jump" method="get" action="">
        <label class="inv-jump-label" for="jump_day">Open day</label>
        <input id="jump_day" name="day" type="date" value="<?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="inv-btn">Go</button>
      </form>
      <p class="inv-hint muted">changing the day field on insert opens that day — not a silent append to today</p>
      <ul class="inv-day-list">
        <?php foreach ($days as $d):
            $dm = json_decode((string) ($d['meta_json'] ?? '{}'), true) ?: [];
            $dkey = (string) ($dm['day'] ?? '');
            $on = ($dkey === $day);
            $cl = !empty($dm['closed']) ? ' closed' : '';
            ?>
          <li>
            <a class="inv-day-link<?= $on ? ' is-on' : '' ?><?= $cl ?>"
               href="<?= $self ?>?day=<?= htmlspecialchars($dkey, ENT_QUOTES, 'UTF-8') ?>">
              <span class="inv-day-key"><?= htmlspecialchars($dkey, ENT_QUOTES, 'UTF-8') ?></span>
              <span class="inv-day-name"><?= htmlspecialchars((string) ($dm['weekday'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
              <?php if (!empty($dm['closed'])): ?>
                <span class="inv-badge">closed</span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
        <?php if (!$days): ?>
          <li class="muted inv-empty">no days yet — insert opens today</li>
        <?php endif; ?>
      </ul>
    </aside>

    <section class="inventury-panel">
      <?php if ($err): ?>
        <p class="inv-status inv-err"><strong>error:</strong> <?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif ($ok): ?>
        <p class="inv-status">
          <strong>captured</strong>
          <?php if ($rpt !== ''): ?>
            · filed to <?= htmlspecialchars($whom !== '' ? $whom : 'skyline', ENT_QUOTES, 'UTF-8') ?>
            · <code><?= htmlspecialchars($rpt, ENT_QUOTES, 'UTF-8') ?></code>
          <?php endif; ?>
        </p>
      <?php endif; ?>

      <?php if (is_array($importFlash)): ?>
        <div class="inv-import-report">
          <p class="inv-status"><strong>vault import</strong>
            · imported <?= (int) ($_GET['imp_n'] ?? 0) ?>
            · skipped <?= (int) ($_GET['skip_n'] ?? 0) ?>
            · errors <?= (int) ($_GET['err_n'] ?? 0) ?>
          </p>
          <?php if (!empty($importFlash['imported'])): ?>
            <ul class="inv-import-list"><?php foreach ($importFlash['imported'] as $line): ?>
              <li><?= htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?></ul>
          <?php endif; ?>
          <?php if (!empty($importFlash['skipped'])): ?>
            <p class="muted">skipped (already had leaves — re-run with force to replace vault-imported only):</p>
            <ul class="inv-import-list"><?php foreach ($importFlash['skipped'] as $line): ?>
              <li><?= htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?></ul>
          <?php endif; ?>
          <?php if (!empty($importFlash['errors'])): ?>
            <ul class="inv-import-list inv-err"><?php foreach ($importFlash['errors'] as $line): ?>
              <li><?= htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?></ul>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <header class="inv-day-head">
        <h2 class="inv-day-title">
          <?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?>
          <span class="muted"><?= htmlspecialchars((string) ($dayMeta['weekday'] ?? date('l', strtotime($day) ?: time())), ENT_QUOTES, 'UTF-8') ?></span>
          <?php if (!$dayExists): ?>
            <span class="inv-badge">not opened yet</span>
          <?php endif; ?>
        </h2>
        <?php if ($dayUid !== ''): ?>
          <form method="post" action="" class="inv-close-form">
            <input type="hidden" name="invent_action" value="<?= $closed ? 'reopen' : 'close' ?>">
            <input type="hidden" name="inv_day_uid" value="<?= htmlspecialchars($dayUid, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="inv_day" value="<?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="inv_tz" class="inv-tz" value="">
            <button type="submit" class="inv-btn"><?= $closed ? 'Reopen day' : 'Close day' ?></button>
          </form>
        <?php endif; ?>
      </header>

      <form method="post" action="" class="inv-insert" id="inv-insert">
        <input type="hidden" name="invent_action" value="insert">
        <input type="hidden" name="inv_tz" class="inv-tz" value="">
        <input type="hidden" name="inv_event_unix" id="inv-event-unix" value="">

        <h3 class="inv-insert-label">Insert · invent leaf</h3>
        <p class="inv-hint muted">
          <strong>Day field decides the log.</strong> Change it → that calendar day is created/opened and the leaf goes there
          (not silently onto today). Optional Skyline copy (mod empty).
          <?php if ($closed): ?> · this day is closed — capture still works (or reopen first)<?php endif; ?>
        </p>

        <label for="inv_day">Day <span class="muted">(this is the log shell · backdate OK)</span></label>
        <input id="inv_day" name="inv_day" type="date" required value="<?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?>">

        <label for="inv_section">Section</label>
        <input id="inv_section" name="inv_section" type="text" list="inv-sections"
               value="INCOMING EVENTS" placeholder="INCOMING EVENTS · omens & signs · …">
        <datalist id="inv-sections">
          <?php foreach ($sectionOrder as $s): ?>
            <option value="<?= htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8') ?>">
          <?php endforeach; ?>
          <option value="INCOMING EVENTS">
          <option value="FINAL DAILY RECORD">
          <option value="omens & signs">
          <option value="notable music">
        </datalist>

        <label for="inv_title">Title / omen line</label>
        <input id="inv_title" name="inv_title" type="text" required
               placeholder="yellow swallowtail · 444 words · …" autocomplete="off">

        <label for="inv_body">What hit</label>
        <textarea id="inv_body" name="inv_body" rows="5"
                  placeholder="what you are reading / noticing while importing…"></textarea>

        <label for="inv_context">Context <span class="muted">(optional)</span></label>
        <input id="inv_context" name="inv_context" type="text"
               placeholder="thinking about CASEY · working on FILES · …">

        <label for="inv_tags">Tags <span class="muted">(optional · Charlie later)</span></label>
        <input id="inv_tags" name="inv_tags" type="text" placeholder="signs/angel-numbers">

        <label for="inv_event">Event time <span class="muted">(optional · backdate · blank = now)</span></label>
        <input id="inv_event" name="inv_event" type="text"
               placeholder="now · 05:02 PM · 2025-09-16 17:02"
               autocomplete="off">

        <label for="inv_report_to">Submit to Skyline report</label>
        <select id="inv_report_to" name="inv_report_to">
          <option value="none">— invent only —</option>
          <?php foreach ($buckets as $key => $b): ?>
            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($b['label'] . '  ·  skyline/services/' . $b['room'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="inv-actions">
          <button type="submit" class="inv-btn inv-btn-primary">Capture leaf</button>
        </div>
      </form>

      <details class="inv-vault" id="inv-vault">
        <summary>Import old vault Daily Inventory (YYMMDD · … .md)</summary>
        <p class="inv-hint muted">
          Parses each dated file into its <strong>own day log + leaves</strong> (not dumped on today).
          Skips days that already have leaves unless force is checked.
          Does <strong>not</strong> dual-write to Skyline (historical only).
        </p>
        <form method="post" action="" class="inv-vault-form">
          <input type="hidden" name="invent_action" value="import_vault">
          <input type="hidden" name="inv_tz" class="inv-tz" value="">
          <label for="inv_vault_dir">Folder</label>
          <input id="inv_vault_dir" name="inv_vault_dir" type="text"
                 value="D:\_Chester's Imports\Terminal IO\USERS\SDK808\Daily Inventory">
          <label class="inv-check">
            <input type="checkbox" name="inv_force" value="1">
            force re-import vault leaves on days that already have them
          </label>
          <div class="inv-actions">
            <button type="submit" class="inv-btn inv-btn-primary">Import vault days</button>
          </div>
        </form>
      </details>

      <div class="inv-leaves">
        <?php if (!$dayExists): ?>
          <p class="muted inv-empty">no log shell for this date yet — capture a leaf or import vault to open it</p>
        <?php elseif (!$entries): ?>
          <p class="muted inv-empty">day open · no leaves yet</p>
        <?php else: ?>
          <?php foreach ($sectionOrder as $sec):
              if (empty($bySection[$sec])) {
                  continue;
              }
              ?>
            <h3 class="inv-sec"><?= htmlspecialchars((string) $sec, ENT_QUOTES, 'UTF-8') ?></h3>
            <?php foreach ($bySection[$sec] as $e) {
                $renderEntry($e);
            } ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<script>
(function () {
  var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
  document.querySelectorAll('.inv-tz').forEach(function (el) { el.value = tz; });

  // Ctrl+Shift+I or Ctrl+. → focus insert title
  document.addEventListener('keydown', function (ev) {
    var mod = ev.ctrlKey || ev.metaKey;
    if (!mod) return;
    if ((ev.shiftKey && (ev.key === 'I' || ev.key === 'i')) || ev.key === '.') {
      var t = document.getElementById('inv_title');
      if (t) {
        ev.preventDefault();
        t.focus();
        t.scrollIntoView({ block: 'center', behavior: 'smooth' });
      }
    }
  });
})();
</script>
