<?php
/**
 * codexDesk · lore of the system (RX · Oriel medicine of meaning)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'list';
if (!in_array($tab, ['list', 'view', 'new', 'edit'], true)) {
    $tab = 'list';
}
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$catFilter = isset($_GET['cat']) ? trim((string) $_GET['cat']) : '';
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';
$err = $GLOBALS['CODEX_ERROR'] ?? null;

$rows = mypi_ledger_list([
    'sys' => 'terminal',
    'dom' => 'rx',
    'room' => 'codex',
    'kind' => 'codex_entry',
    'tool' => 'codexDesk',
    'limit' => 150,
    'order' => 'desc',
]);

if ($q !== '' || $catFilter !== '') {
    $ql = strtolower($q);
    $rows = array_values(array_filter($rows, static function ($r) use ($ql, $catFilter) {
        $m = json_decode((string) ($r['meta_json'] ?? '{}'), true) ?: [];
        if ($catFilter !== '' && strtolower((string) ($m['category'] ?? '')) !== strtolower($catFilter)) {
            return false;
        }
        if ($ql === '') {
            return true;
        }
        $blob = strtolower(
            ($r['topic'] ?? '') . ' ' . ($r['body'] ?? '') . ' ' . ($r['tags_raw'] ?? '')
            . ' ' . ($m['kven'] ?? '') . ' ' . ($m['category'] ?? '')
        );
        return str_contains($blob, $ql);
    }));
}

$focus = null;
$focusMeta = [];
if ($id !== '' && in_array($tab, ['view', 'edit'], true)) {
    $focus = mypi_ledger_get($id);
    if ($focus && ($focus['kind'] ?? '') === 'codex_entry') {
        $focusMeta = json_decode((string) ($focus['meta_json'] ?? '{}'), true) ?: [];
    } else {
        $focus = null;
        $tab = 'list';
    }
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};

$cats = [
    'system' => 'system',
    'person' => 'person',
    'place' => 'place',
    'event' => 'event',
    'tech' => 'tech',
    'world' => 'world',
    'other' => 'other',
];
?>
<div class="cxdesk" id="codex-desk">
  <div class="cxdesk-layout">
    <aside class="cxdesk-side">
      <div class="cxdesk-brand">
        <span class="cxdesk-chip">CODEX</span>
        <span class="cxdesk-whisper">lore of the system</span>
      </div>
      <form method="get" class="cx-form" style="padding:0.35rem;margin:0">
        <input type="hidden" name="tab" value="list">
        <input type="text" name="q" value="<?= $h($q) ?>" placeholder="search lore…" style="font-size:0.85rem">
        <select name="cat" style="margin-top:0.35rem;width:100%;font:inherit;color:inherit;background:rgba(0,0,0,0.3);border:1px solid color-mix(in srgb,currentColor 30%,transparent);padding:0.3rem">
          <option value="">all categories</option>
          <?php foreach ($cats as $k => $lab): ?>
            <option value="<?= $k ?>"<?= $catFilter === $k ? ' selected' : '' ?>><?= $lab ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="cx-btn" style="margin-top:0.35rem">filter</button>
      </form>
      <p class="cx-hint"><a class="cx-btn" href="<?= $self ?>?tab=new">+ entry</a></p>
      <ul class="cxdesk-list">
        <?php if (!$rows): ?>
          <li class="cx-empty">no lore yet · write the house down</li>
        <?php endif; ?>
        <?php foreach ($rows as $r):
            $m = json_decode((string) ($r['meta_json'] ?? '{}'), true) ?: [];
            $on = ($id === $r['c_uid']);
            ?>
          <li>
            <a class="<?= $on ? 'is-on' : '' ?>"
               href="<?= $self ?>?tab=view&amp;id=<?= rawurlencode($r['c_uid']) ?>">
              <?= $h((string) $r['topic']) ?>
              <span class="cx-meta">
                <?= $h((string) ($m['category'] ?? 'system')) ?>
                <?php if (!empty($m['kven'])): ?>
                  · <?= $h((string) $m['kven']) ?>
                <?php endif; ?>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <section class="cxdesk-panel">
      <?php if ($err): ?>
        <p class="cx-status cx-err"><strong>error:</strong> <?= $h((string) $err) ?></p>
      <?php elseif ($ok !== ''): ?>
        <p class="cx-status"><strong>ok</strong> · <?= $h($ok) ?></p>
      <?php endif; ?>

      <?php if ($tab === 'new' || ($tab === 'edit' && $focus)): ?>
        <?php
        $f = $focus ?: null;
        $fm = $f ? $focusMeta : [];
        ?>
        <h2 class="cxdesk-title"><?= $f ? 'Edit entry' : 'New codex entry' ?></h2>
        <p class="cx-hint">Not a VEN code. Not a shot. Not evidence. <strong>Lore of the system</strong> — how the house works, who started what, what we believe is true enough to keep.</p>
        <form method="post" class="cx-form">
          <input type="hidden" name="cx_action" value="save">
          <input type="hidden" name="cx_tz" id="cx-tz" value="">
          <?php if ($f): ?>
            <input type="hidden" name="cx_c_uid" value="<?= $h($f['c_uid']) ?>">
          <?php endif; ?>
          <label>Title</label>
          <input name="cx_title" type="text" required
                 value="<?= $h((string) ($f['topic'] ?? '')) ?>"
                 placeholder="Aubel · IOX master crossover · …">
          <label>Category</label>
          <select name="cx_category">
            <?php
            $cur = (string) ($fm['category'] ?? 'system');
            foreach ($cats as $k => $lab):
            ?>
              <option value="<?= $k ?>"<?= $cur === $k ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
          <label>Linked KVEN <span class="muted">(optional · e.g. ABL-000)</span></label>
          <input name="cx_kven" type="text" maxlength="8"
                 value="<?= $h((string) ($fm['kven'] ?? '')) ?>"
                 placeholder="ABC-123" style="text-transform:uppercase">
          <label>Body</label>
          <textarea name="cx_body" required placeholder="what is true enough to keep…"><?= $h((string) ($f['body'] ?? '')) ?></textarea>
          <label>Tags</label>
          <input name="cx_tags" type="text"
                 value="<?= $h((string) ($f['tags_raw'] ?? 'codex,lore')) ?>"
                 placeholder="codex, lore, aubel, iox">
          <div class="cx-row">
            <button type="submit" class="cx-btn cx-btn-primary"><?= $f ? 'Update' : 'File lore' ?></button>
            <a class="cx-btn" href="<?= $self ?>?tab=list">cancel</a>
          </div>
        </form>

      <?php elseif ($tab === 'view' && $focus): ?>
        <?php
        $cat = (string) ($focusMeta['category'] ?? 'system');
        $kven = (string) ($focusMeta['kven'] ?? '');
        ?>
        <header class="cxdesk-head">
          <h2 class="cxdesk-title"><?= $h((string) $focus['topic']) ?></h2>
          <span class="cx-type"><?= $h($cat) ?><?php if ($kven !== ''): ?> · <?= $h($kven) ?><?php endif; ?></span>
        </header>
        <div class="cx-body">
          <?php
          $body = (string) ($focus['body'] ?? '');
          if (function_exists('render_md') && $body !== '') {
              echo render_md($body);
          } else {
              echo nl2br($h($body));
          }
          ?>
        </div>
        <div class="cx-row" style="margin-top:0.85rem">
          <a class="cx-btn cx-btn-primary" href="<?= $self ?>?tab=edit&amp;id=<?= rawurlencode($focus['c_uid']) ?>">edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('soft-delete this lore entry?');">
            <input type="hidden" name="cx_action" value="delete">
            <input type="hidden" name="cx_c_uid" value="<?= $h($focus['c_uid']) ?>">
            <button type="submit" class="cx-btn">delete</button>
          </form>
          <a class="cx-btn" href="<?= $self ?>?tab=list">all lore</a>
        </div>

      <?php else: ?>
        <h2 class="cxdesk-title">Codex</h2>
        <p class="cx-lede">
          Lore of the system. Where Aubel is shady. Where Chester vanishes.
          Where IOX crossover began. Where Genesis translations get tracked even when Jack Weak has no terminal.
          VEN holds codes. <strong>Codex holds the story of the codes.</strong>
        </p>
        <p><a class="cx-btn cx-btn-primary" href="<?= $self ?>?tab=new">+ write lore</a></p>
        <?php if ($rows): ?>
          <ul class="cx-index">
            <?php foreach ($rows as $r):
                $m = json_decode((string) ($r['meta_json'] ?? '{}'), true) ?: [];
                ?>
              <li>
                <a href="<?= $self ?>?tab=view&amp;id=<?= rawurlencode($r['c_uid']) ?>">
                  <strong><?= $h((string) $r['topic']) ?></strong>
                  <span class="cx-meta"><?= $h((string) ($m['category'] ?? 'system')) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  </div>
</div>
<script>
(function () {
  try {
    var el = document.getElementById('cx-tz');
    if (el) el.value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
  } catch (e) {}
})();
</script>
