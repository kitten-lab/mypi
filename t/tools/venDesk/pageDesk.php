<?php
/**
 * venDesk · Oriel RX · code book
 */
require_once __DIR__ . '/venDesk_lib.php';

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'list';
if (!in_array($tab, ['list', 'view', 'new', 'edit'], true)) {
    $tab = 'list';
}
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';
$err = $GLOBALS['VEN_ERROR'] ?? null;

$reg = vendesk_load();
$entries = $reg['entries'];

if ($q !== '') {
    $ql = strtolower($q);
    $entries = array_values(array_filter($entries, static function ($e) use ($ql) {
        $blob = strtolower(implode(' ', [
            $e['kven'] ?? '',
            $e['label'] ?? '',
            $e['type'] ?? '',
            $e['notes'] ?? '',
            implode(' ', $e['alts'] ?? []),
            implode(' ', $e['matches'] ?? []),
        ]));
        return str_contains($blob, $ql);
    }));
}

$focus = null;
if ($id !== '' && in_array($tab, ['view', 'edit'], true)) {
    $focus = vendesk_find($reg, $id);
    if (!$focus) {
        $tab = 'list';
    } else {
        $id = $focus['id'];
    }
}
if ($tab === 'new') {
    $focus = null;
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');
$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};
$types = ['person' => 'person', 'place' => 'place', 'object' => 'object', 'event' => 'event', 'other' => 'other'];
?>
<div class="vdesk" id="ven-desk">
  <div class="vdesk-layout">
    <aside class="vdesk-side">
      <div class="vdesk-brand">
        <span class="vdesk-chip">VEN</span>
        <span class="vdesk-whisper">code book · medicine</span>
      </div>
      <form method="get" action="" class="vd-form" style="padding:0.35rem;margin:0">
        <input type="hidden" name="tab" value="list">
        <input type="text" name="q" value="<?= $h($q) ?>" placeholder="search codes…" style="font-size:0.85rem">
      </form>
      <p class="vd-hint">
        <a class="vd-btn" href="<?= $self ?>?tab=new">+ ven</a>
      </p>
      <ul class="vdesk-list">
        <?php if (!$entries): ?>
          <li class="vd-empty">no codes yet · open the book</li>
        <?php endif; ?>
        <?php foreach ($entries as $e):
            $on = ($id === ($e['id'] ?? ''));
            $alts = $e['alts'] ?? [];
            ?>
          <li>
            <a class="<?= $on ? 'is-on' : '' ?>"
               href="<?= $self ?>?tab=view&amp;id=<?= rawurlencode((string) $e['id']) ?>">
              <code><?= $h((string) $e['kven']) ?></code>
              <span class="vd-meta">
                <?= $h((string) ($e['type'] ?? 'other')) ?>
                <?php if ($alts): ?>
                  · <?= $h(implode(', ', array_slice($alts, 0, 2))) ?>
                <?php elseif (!empty($e['label'])): ?>
                  · <?= $h((string) $e['label']) ?>
                <?php endif; ?>
              </span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <section class="vdesk-panel">
      <?php if ($err): ?>
        <p class="vd-status vd-err"><strong>error:</strong> <?= $h((string) $err) ?></p>
      <?php elseif ($ok !== ''): ?>
        <p class="vd-status"><strong>ok</strong> · <?= $h($ok) ?></p>
      <?php endif; ?>

      <?php if ($tab === 'new' || ($tab === 'edit' && $focus) || ($tab === 'view' && $focus && isset($_GET['edit']))): ?>
        <?php
        $isEdit = is_array($focus);
        $f = $focus ?: [
            'id' => '', 'kven' => '', 'label' => '', 'alts' => [], 'matches' => [],
            'notes' => '', 'type' => 'person',
        ];
        ?>
        <h2 class="vdesk-title"><?= $isEdit ? 'Edit VEN' : 'New VEN' ?></h2>
        <p class="vd-hint">
          KVEN = code · alts = also written as / spoken · label = guarded true name · matches = log spellings
        </p>
        <form method="post" class="vd-form">
          <input type="hidden" name="vd_action" value="save">
          <input type="hidden" name="vd_id" value="<?= $h((string) ($f['id'] ?? '')) ?>">
          <label for="vd_kven">KVEN <span class="muted">(ABC-123 · blank = auto)</span></label>
          <input id="vd_kven" name="vd_kven" type="text" maxlength="7"
                 value="<?= $h((string) ($f['kven'] ?? '')) ?>"
                 placeholder="HJI-001" pattern="[A-Za-z]{3}-?[0-9]{0,3}" style="text-transform:uppercase">
          <label for="vd_type">Type</label>
          <select id="vd_type" name="vd_type">
            <?php foreach ($types as $k => $lab): ?>
              <option value="<?= $k ?>"<?= ($f['type'] ?? 'person') === $k ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
          </select>
          <label for="vd_alts">Alts · also written as <span class="muted">(comma-separated)</span></label>
          <input id="vd_alts" name="vd_alts" type="text"
                 value="<?= $h(implode(', ', $f['alts'] ?? [])) ?>"
                 placeholder="Haji, the import clerk">
          <label for="vd_label">Label <span class="muted">(guarded true name · discreet)</span></label>
          <input id="vd_label" name="vd_label" type="text"
                 value="<?= $h((string) ($f['label'] ?? '')) ?>"
                 placeholder="enough to remember · not for the yard">
          <label for="vd_matches">Matches <span class="muted">(how it appears in logs)</span></label>
          <input id="vd_matches" name="vd_matches" type="text"
                 value="<?= $h(implode(', ', $f['matches'] ?? [])) ?>"
                 placeholder="full name spellings, nicknames in glass">
          <label for="vd_notes">Notes</label>
          <textarea id="vd_notes" name="vd_notes" placeholder="forester · Jack Weak track · prophecy link…"><?= $h((string) ($f['notes'] ?? '')) ?></textarea>
          <div class="vd-row">
            <button type="submit" class="vd-btn vd-btn-primary"><?= $isEdit ? 'Update' : 'File code' ?></button>
            <?php if ($isEdit): ?>
              <a class="vd-btn" href="<?= $self ?>?tab=view&amp;id=<?= rawurlencode((string) $f['id']) ?>">cancel</a>
            <?php else: ?>
              <a class="vd-btn" href="<?= $self ?>?tab=list">cancel</a>
            <?php endif; ?>
          </div>
        </form>

      <?php elseif ($tab === 'view' && $focus): ?>
        <header class="vdesk-head">
          <h2 class="vdesk-title"><code><?= $h((string) $focus['kven']) ?></code></h2>
          <span class="vd-type"><?= $h((string) ($focus['type'] ?? 'other')) ?></span>
        </header>
        <?php if (!empty($focus['alts'])): ?>
          <p class="vd-alts">
            <span class="vd-k">alts</span>
            <?= $h(implode(' · ', $focus['alts'])) ?>
          </p>
        <?php endif; ?>
        <?php if (trim((string) ($focus['label'] ?? '')) !== ''): ?>
          <p class="vd-label-row">
            <span class="vd-k">label</span>
            <span class="vd-label-val"><?= $h((string) $focus['label']) ?></span>
          </p>
        <?php endif; ?>
        <?php if (!empty($focus['matches'])): ?>
          <p class="vd-matches">
            <span class="vd-k">matches</span>
            <?= $h(implode(' · ', $focus['matches'])) ?>
          </p>
        <?php endif; ?>
        <?php if (trim((string) ($focus['notes'] ?? '')) !== ''): ?>
          <div class="vd-notes"><?= nl2br($h((string) $focus['notes'])) ?></div>
        <?php endif; ?>
        <div class="vd-row" style="margin-top:0.85rem">
          <a class="vd-btn vd-btn-primary" href="<?= $self ?>?tab=edit&amp;id=<?= rawurlencode((string) $focus['id']) ?>">edit</a>
          <form method="post" style="display:inline" onsubmit="return confirm('remove this VEN from the book?');">
            <input type="hidden" name="vd_action" value="delete">
            <input type="hidden" name="vd_id" value="<?= $h((string) $focus['id']) ?>">
            <button type="submit" class="vd-btn">delete</button>
          </form>
        </div>

      <?php else: ?>
        <h2 class="vdesk-title">VEN desk</h2>
        <p class="vd-lede">
          Oriel’s code book. Match stories to codes. Skyline-shaped language without trusting the Corporation.
          True names stay guarded. LogImport will link these codes later — craft them here.
        </p>
        <p class="vd-hint">
          registry · <code>z/ven_registry/</code> · <?= count($reg['entries']) ?> codes
          <?php if (!empty($reg['updated_at'])): ?>
            · updated <?= $h(date('Y-m-d H:i', (int) $reg['updated_at'])) ?>
          <?php endif; ?>
        </p>
        <p><a class="vd-btn vd-btn-primary" href="<?= $self ?>?tab=new">+ file a code</a></p>
        <?php if ($entries): ?>
          <ul class="vd-index">
            <?php foreach ($entries as $e): ?>
              <li>
                <a href="<?= $self ?>?tab=view&amp;id=<?= rawurlencode((string) $e['id']) ?>">
                  <code><?= $h((string) $e['kven']) ?></code>
                  <strong><?= $h((string) (($e['alts'][0] ?? '') ?: ($e['label'] ?? ''))) ?></strong>
                  <span class="vd-meta"><?= $h((string) ($e['type'] ?? '')) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  </div>
</div>
