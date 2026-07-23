<?php
/**
 * cmsBASIC · mythleak headlines desk (list / view / write)
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

$equip = ROUTE_TO_SYSTEMS . 'Borrows/parsedown/equip.parsedown.php';
if (is_file($equip)) {
    require_once $equip;
}

$mode = isset($_GET['cms']) ? (string) $_GET['cms'] : '';
$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$ok = isset($_GET['ok']) ? (string) $_GET['ok'] : '';
$err = $GLOBALS['CMS_ERROR'] ?? null;

// room can force mode
$room = defined('ROOM_SLUG') ? strtolower((string) ROOM_SLUG) : 'headlines';
if ($mode === '') {
    if ($room === 'write') {
        $mode = 'write';
    } elseif ($room === 'article' || $id !== '') {
        $mode = 'view';
    } else {
        $mode = 'list';
    }
}

$place = [
    'sys' => 'mythleak',
    'dom' => 'news',
    'room' => 'headlines',
];
if (function_exists('mypi_ledger_place_from_sky')) {
    $sky = mypi_ledger_place_from_sky();
    if (!empty($sky['sys'])) {
        $place['sys'] = $sky['sys'];
    }
    if (!empty($sky['dom'])) {
        $place['dom'] = $sky['dom'];
    }
}

$rows = mypi_ledger_list([
    'sys' => $place['sys'],
    'dom' => $place['dom'],
    'room' => 'headlines',
    'kind' => 'headline',
    'tool' => 'cmsBASIC',
    'limit' => 80,
    'order' => 'desc',
]);

// also show tool-less seeds
if (!$rows) {
    $rows = mypi_ledger_list([
        'sys' => $place['sys'],
        'kind' => 'headline',
        'limit' => 80,
        'order' => 'desc',
    ]);
}

$focus = null;
$focusMeta = [];
if ($id !== '' && $mode === 'view') {
    $focus = mypi_ledger_get($id);
    if ($focus && ($focus['kind'] ?? '') === 'headline') {
        $focusMeta = json_decode((string) ($focus['meta_json'] ?? '{}'), true) ?: [];
    } else {
        $focus = null;
        $mode = 'list';
    }
}

$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};

/**
 * Dek + body split: vault seeds often stash subhead as first body line.
 * Never print that line twice.
 */
$splitDekBody = static function (array $row, array $meta): array {
    $body = (string) ($row['body'] ?? '');
    $dek = trim((string) ($meta['dek'] ?? ''));
    $lines = preg_split('/\R/', $body) ?: [];
    $first = trim((string) ($lines[0] ?? ''));

    if ($dek === '' && $first !== '') {
        // ALL-CAPS-ish or short first line = subhead energy
        $looksDek = (strlen($first) <= 200)
            && ($first === strtoupper($first) || strlen($first) < 120)
            && !preg_match('/^[a-z]/', $first);
        if ($looksDek) {
            $dek = $first;
            $body = trim(implode("\n", array_slice($lines, 1)));
        }
    } elseif ($dek !== '' && $first !== '' && strcasecmp($dek, $first) === 0) {
        $body = trim(implode("\n", array_slice($lines, 1)));
    }

    return ['dek' => $dek, 'body' => $body];
};

$dekOf = static function (array $row, array $meta) use ($splitDekBody): string {
    return $splitDekBody($row, $meta)['dek'];
};
?>
<div class="cmsb" id="cms-basic">
  <?php if ($err): ?>
    <p class="cmsb-status cmsb-err"><strong>error:</strong> <?= $h((string) $err) ?></p>
  <?php elseif ($ok !== ''): ?>
    <p class="cmsb-status"><strong>filed</strong> · <?= $h($ok) ?></p>
  <?php endif; ?>

  <?php if ($mode === 'write'): ?>
    <h1>FILE A LEAK</h1>
    <p class="cmsb-lede">
      K + mouse write by hand. No auto-headlines. Store full text here; leak fragments to social later.
    </p>
    <form method="post" class="cmsb-form" action="/mythleak/news/write">
      <input type="hidden" name="cms_action" value="save">
      <input type="hidden" name="cms_tz" id="cms-tz" value="">
      <label for="cms_title">Headline</label>
      <input id="cms_title" name="cms_title" type="text" required placeholder="ALL CAPS ENERGY OPTIONAL">
      <label for="cms_dek">Dek / subhead</label>
      <input id="cms_dek" name="cms_dek" type="text" placeholder="one vicious line (optional if first body line is the dek)">
      <label for="cms_event">Event date <span style="opacity:.6">(when it happened — not “filed today”)</span></label>
      <input id="cms_event" name="cms_event" type="date" value="">
      <label for="cms_byline">Byline</label>
      <input id="cms_byline" name="cms_byline" type="text"
             value="<?= $h(defined('MOD_DISPLAY') ? (string) MOD_DISPLAY : '-/mouse') ?>"
             placeholder="-MOUSE- or Agent K">
      <label for="cms_body">Body</label>
      <textarea id="cms_body" name="cms_body" required placeholder="the gods deserve this"></textarea>
      <label for="cms_tags">Tags</label>
      <input id="cms_tags" name="cms_tags" type="text" value="mythleak,headline" placeholder="zero, holy, skyline">
      <button type="submit">Publish to the juice line</button>
      <a class="cmsb-btn" href="/mythleak/news/headlines">cancel</a>
    </form>

  <?php elseif ($mode === 'view' && $focus): ?>
    <?php
    $split = $splitDekBody($focus, $focusMeta);
    $dek = $split['dek'];
    $body = $split['body'];
    $by = (string) ($focusMeta['byline'] ?? $focus['actor'] ?? $focus['agent'] ?? 'staff');
    $when = (int) ($focus['event_unix'] ?? $focus['ingest_unix'] ?? 0);
    $whenS = $when > 0 ? date('Y-m-d', $when) : '';
    ?>
    <article class="cmsb-article">
      <h1 class="cmsb-article-hed"><?= $h((string) $focus['topic']) ?></h1>
      <p class="cmsb-article-meta">
        by <?= $h($by) ?>
        <?php if ($whenS !== ''): ?> · <time datetime="<?= $h($whenS) ?>"><?= $h($whenS) ?></time><?php endif; ?>
        · <?= $h((string) $focus['c_uid']) ?>
      </p>
      <?php if ($dek !== ''): ?>
        <p class="cmsb-dek" style="margin-bottom:0.85rem;color:#faa"><?= $h($dek) ?></p>
      <?php endif; ?>
      <div class="cmsb-body">
        <?php
        if (function_exists('render_md') && $body !== '') {
            echo render_md($body);
        } else {
            echo nl2br($h($body));
        }
        ?>
      </div>
      <p style="margin-top:1rem">
        <a href="/mythleak/news/headlines">← back to headlines</a>
        ·
        <a href="/mythleak/news/write">file another</a>
      </p>
    </article>

  <?php else: ?>
    <h1>HEADLINES</h1>
    <p class="cmsb-lede">
      THE GODS ARE REAL and we have the reciepts. Satirical dirt from the Skyline —
      filed by hand, stored in the pocket, leaked in pieces.
    </p>
    <p><a class="cmsb-btn" href="/mythleak/news/write">+ file a leak</a></p>
    <?php if (!$rows): ?>
      <p class="cmsb-empty">no headlines yet · the juice line is thirsty</p>
    <?php else: ?>
      <ul class="cmsb-list">
        <?php foreach ($rows as $row):
            $m = json_decode((string) ($row['meta_json'] ?? '{}'), true) ?: [];
            $dek = $dekOf($row, $m);
            $by = (string) ($m['byline'] ?? $row['actor'] ?? $row['agent'] ?? '');
            $when = (int) ($row['event_unix'] ?? $row['ingest_unix'] ?? 0);
            $whenS = $when > 0 ? date('Y-m-d', $when) : '';
            ?>
          <li>
            <a class="cmsb-hed" href="/mythleak/news/article?id=<?= rawurlencode($row['c_uid']) ?>">
              <?= $h((string) $row['topic']) ?>
            </a>
            <?php if ($dek !== ''): ?>
              <span class="cmsb-dek"><?= $h($dek) ?></span>
            <?php endif; ?>
            <span class="cmsb-meta">
              <?php if ($by !== ''): ?>by <?= $h($by) ?><?php endif; ?>
              <?php if ($whenS !== ''): ?> · <?= $h($whenS) ?><?php endif; ?>
              · continue reading →
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>
</div>
<script>
(function () {
  try {
    var el = document.getElementById('cms-tz');
    if (el) el.value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
  } catch (e) {}
})();
</script>
