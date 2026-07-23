<?php
/**
 * shotDesk · Watchers scene cards (ICU)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$place = mypi_ledger_place_from_sky();
$sys = $place['sys'] !== '' ? $place['sys'] : 'terminal';
$dom = $place['dom'] !== '' ? $place['dom'] : 'icu';
$room = 'shots';
$basePlace = [
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
];

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'list';
if (!in_array($tab, ['list', 'shot', 'new'], true)) {
    $tab = 'list';
}
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$err = $GLOBALS['SHOT_ERROR'] ?? null;
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';

$shots = mypi_shot_list(array_merge($basePlace, ['q' => $q, 'limit' => 150]));

$focus = null;
$focusMeta = [];
if ($id !== '') {
    $focus = mypi_ledger_get($id);
    if ($focus && ($focus['kind'] ?? '') === 'shot_card') {
        $focusMeta = json_decode((string) ($focus['meta_json'] ?? '{}'), true) ?: [];
        $tab = 'shot';
    } else {
        $focus = null;
    }
}

$self = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', ENT_QUOTES, 'UTF-8');

$imgSrc = static function (string $assetId): string {
    if ($assetId === '' || !function_exists('mypi_media_img_src')) {
        return '';
    }
    return mypi_media_img_src($assetId);
};

$field = static function (array $meta, string $key, string $fallback = ''): string {
    $v = trim((string) ($meta[$key] ?? ''));
    return $v !== '' ? $v : $fallback;
};
?>
<div class="sdesk" id="shot-desk">
  <div class="sdesk-layout">
    <aside class="sdesk-side">
      <div class="sdesk-brand">
        <span class="sdesk-chip">SHOTS</span>
        <span class="sdesk-whisper">watchers · i see you</span>
      </div>
      <form method="get" action="" class="sd-form" style="padding:0.35rem;margin:0">
        <input type="hidden" name="tab" value="list">
        <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="search cards…" style="font-size:0.85rem">
      </form>
      <p class="sd-hint">
        <a class="sd-btn" href="<?= $self ?>?tab=new">+ shot</a>
      </p>
      <ul class="sdesk-list">
        <?php if (!$shots): ?>
          <li class="sd-empty">no shot cards yet · first claim waiting</li>
        <?php endif; ?>
        <?php foreach ($shots as $s):
            $sm = json_decode((string) ($s['meta_json'] ?? '{}'), true) ?: [];
            $on = ($id === $s['c_uid']);
            $slug = trim((string) ($sm['slugline'] ?? ''));
            ?>
          <li>
            <a class="<?= $on ? 'is-on' : '' ?>"
               href="<?= $self ?>?tab=shot&amp;id=<?= rawurlencode($s['c_uid']) ?>">
              <?= htmlspecialchars((string) $s['topic'], ENT_QUOTES, 'UTF-8') ?>
              <?php if ($slug !== ''): ?>
                <span class="sd-meta"><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>

    <section class="sdesk-panel">
      <?php if ($err): ?>
        <p class="sd-status sd-err"><strong>error:</strong> <?= htmlspecialchars((string) $err, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif ($ok !== ''): ?>
        <p class="sd-status"><strong>saved</strong> · <?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <?php if ($tab === 'new' || ($tab === 'shot' && !$focus)): ?>
        <h2 class="sdesk-title">New shot card</h2>
        <p class="sd-hint">production material · not AB evidence · not K’s desk</p>
        <form method="post" class="sd-form">
          <input type="hidden" name="sd_action" value="save_shot">
          <input type="hidden" name="sd_tz" class="sd-tz" value="">
          <label for="sd_title">Title / code</label>
          <input id="sd_title" name="sd_title" type="text" required placeholder="e.g. e6O error log">
          <label for="sd_slugline">Slugline / location</label>
          <input id="sd_slugline" name="sd_slugline" type="text" placeholder="INT. OPS ROOM — NIGHT">
          <label for="sd_visual">Visual</label>
          <textarea id="sd_visual" name="sd_visual" placeholder="what the camera sees"></textarea>
          <label for="sd_action_body">Action</label>
          <textarea id="sd_action_body" name="sd_action_body" placeholder="what bodies do"></textarea>
          <label for="sd_dialogue">Dialogue</label>
          <textarea id="sd_dialogue" name="sd_dialogue" placeholder="lines · robotic VO · whispers"></textarea>
          <label for="sd_transition">Transition</label>
          <textarea id="sd_transition" name="sd_transition" placeholder="fade to stupid black…"></textarea>
          <label for="sd_amusement">Amusement note</label>
          <textarea id="sd_amusement" name="sd_amusement" placeholder="why this forces main-character energy"></textarea>
          <label for="sd_tags">Tone tags</label>
          <input id="sd_tags" name="sd_tags" type="text" placeholder="alert, wire-death, sweater-girl">
          <div class="sd-row">
            <button type="submit" class="sd-btn sd-btn-primary">Save shot</button>
          </div>
        </form>

      <?php elseif ($tab === 'shot' && $focus): ?>
        <?php
        $sm = $focusMeta;
        $board = (string) ($sm['storyboard_asset'] ?? '');
        $bSrc = $board !== '' ? $imgSrc($board) : '';
        if ($bSrc === '' && !empty($sm['attachments'][0]['asset_id'])) {
            $bSrc = $imgSrc((string) $sm['attachments'][0]['asset_id']);
        }
        $slugline = $field($sm, 'slugline');
        $visual = $field($sm, 'visual');
        $actionBody = $field($sm, 'action');
        $dialogue = $field($sm, 'dialogue');
        $transition = $field($sm, 'transition');
        $amusement = $field($sm, 'amusement');
        ?>
        <header class="sdesk-head">
          <h2 class="sdesk-title"><?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?></h2>
          <span class="sd-code"><?= htmlspecialchars((string) ($sm['code'] ?? $focus['topic']), ENT_QUOTES, 'UTF-8') ?></span>
        </header>

        <?php if ($slugline !== ''): ?>
          <p class="sd-slugline"><?= htmlspecialchars($slugline, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($bSrc !== ''): ?>
          <img class="sd-board" src="<?= htmlspecialchars($bSrc, ENT_QUOTES, 'UTF-8') ?>" alt="storyboard">
        <?php endif; ?>

        <div class="sd-card-view">
          <?php if ($visual !== ''): ?>
            <div class="sd-block">
              <h3 class="sd-section">Visual</h3>
              <div class="sd-body"><?php if (function_exists('render_md')) {
                  echo render_md($visual);
              } else {
                  echo nl2br(htmlspecialchars($visual, ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
          <?php if ($actionBody !== ''): ?>
            <div class="sd-block">
              <h3 class="sd-section">Action</h3>
              <div class="sd-body"><?php if (function_exists('render_md')) {
                  echo render_md($actionBody);
              } else {
                  echo nl2br(htmlspecialchars($actionBody, ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
          <?php if ($dialogue !== ''): ?>
            <div class="sd-block sd-dialogue">
              <h3 class="sd-section">Dialogue</h3>
              <div class="sd-body"><?php if (function_exists('render_md')) {
                  echo render_md($dialogue);
              } else {
                  echo nl2br(htmlspecialchars($dialogue, ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
          <?php if ($transition !== ''): ?>
            <div class="sd-block">
              <h3 class="sd-section">Transition</h3>
              <div class="sd-body sd-transition"><?php if (function_exists('render_md')) {
                  echo render_md($transition);
              } else {
                  echo nl2br(htmlspecialchars($transition, ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
          <?php if ($amusement !== ''): ?>
            <div class="sd-block sd-amuse">
              <h3 class="sd-section">Amusement</h3>
              <div class="sd-body"><?php if (function_exists('render_md')) {
                  echo render_md($amusement);
              } else {
                  echo nl2br(htmlspecialchars($amusement, ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
          <?php if ($visual === '' && $actionBody === '' && $dialogue === '' && trim((string) $focus['body']) !== ''): ?>
            <div class="sd-block">
              <div class="sd-body"><?php if (function_exists('render_md')) {
                  echo render_md((string) $focus['body']);
              } else {
                  echo nl2br(htmlspecialchars((string) $focus['body'], ENT_QUOTES, 'UTF-8'));
              } ?></div>
            </div>
          <?php endif; ?>
        </div>

        <h3 class="sd-section">Edit card</h3>
        <form method="post" class="sd-form">
          <input type="hidden" name="sd_action" value="save_shot">
          <input type="hidden" name="sd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="sd_tz" class="sd-tz" value="">
          <label>Title / code</label>
          <input name="sd_title" type="text" required value="<?= htmlspecialchars((string) $focus['topic'], ENT_QUOTES, 'UTF-8') ?>">
          <label>Slugline</label>
          <input name="sd_slugline" type="text" value="<?= htmlspecialchars($slugline, ENT_QUOTES, 'UTF-8') ?>">
          <label>Visual</label>
          <textarea name="sd_visual"><?= htmlspecialchars($visual, ENT_QUOTES, 'UTF-8') ?></textarea>
          <label>Action</label>
          <textarea name="sd_action_body"><?= htmlspecialchars($actionBody, ENT_QUOTES, 'UTF-8') ?></textarea>
          <label>Dialogue</label>
          <textarea name="sd_dialogue"><?= htmlspecialchars($dialogue, ENT_QUOTES, 'UTF-8') ?></textarea>
          <label>Transition</label>
          <textarea name="sd_transition"><?= htmlspecialchars($transition, ENT_QUOTES, 'UTF-8') ?></textarea>
          <label>Amusement</label>
          <textarea name="sd_amusement"><?= htmlspecialchars($amusement, ENT_QUOTES, 'UTF-8') ?></textarea>
          <label>Tone tags</label>
          <input name="sd_tags" type="text" value="<?= htmlspecialchars((string) ($focus['tags_raw'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          <div class="sd-row"><button type="submit" class="sd-btn sd-btn-primary">Update shot</button></div>
        </form>

        <form method="post" enctype="multipart/form-data" class="sd-form">
          <input type="hidden" name="sd_action" value="attach">
          <input type="hidden" name="sd_c_uid" value="<?= htmlspecialchars($focus['c_uid'], ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="sd_media_role" value="storyboard">
          <input type="hidden" name="sd_tz" class="sd-tz" value="">
          <label>Storyboard image</label>
          <input type="file" name="sd_image" accept="image/*" required>
          <div class="sd-row"><button type="submit" class="sd-btn">Install board</button></div>
        </form>

      <?php else: ?>
        <h2 class="sdesk-title">Shot desk</h2>
        <p class="sd-lede">
          Scene cards for the show. AB investigates the wires as if they are real.
          ICU records the set. Pick a card · or claim a new cut.
        </p>
        <?php if ($shots): ?>
          <ul class="sd-index">
            <?php foreach ($shots as $s):
                $sm = json_decode((string) ($s['meta_json'] ?? '{}'), true) ?: [];
                ?>
              <li>
                <a href="<?= $self ?>?tab=shot&amp;id=<?= rawurlencode($s['c_uid']) ?>">
                  <strong><?= htmlspecialchars((string) $s['topic'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <?php if (!empty($sm['slugline'])): ?>
                    <span class="sd-meta"><?= htmlspecialchars((string) $sm['slugline'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="sd-empty">empty shelf · <a href="<?= $self ?>?tab=new">first shot</a></p>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  </div>
</div>
<script>
(function () {
  try {
    var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
    document.querySelectorAll('.sd-tz').forEach(function (el) { el.value = tz; });
  } catch (e) {}
})();
</script>
