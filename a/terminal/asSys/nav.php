<?php
/**
 * Station tree — archive-ish list, not polished vault explorer.
 * Only THIS station when logged in (no tourism to base).
 */
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}
require_once (defined('echoSONAR') ? echoSONAR : dirname(__DIR__, 3) . '/') . 'k/puppies/authSession.puppy.php';
mypi_auth_boot();

$dom = defined('DOM_SLUG') ? DOM_SLUG : 'base';
$room = defined('ROOM_SLUG') ? ROOM_SLUG : '';
$authed = mypi_auth_check();
$agent = mypi_auth_agent();
$domL = strtolower((string) $dom);

$h = function ($d, $key) {
    return function_exists('mypi_room_href')
        ? mypi_room_href($d, $key)
        : ('/terminal/' . $d . '/' . $key);
};

// IO — full green house · IMPORTS is a desk drawer with two bays
$treeIo = [
    [
        'key' => 'imports',
        'label' => 'IMPORTS',
        'children' => [
            ['key' => 'import', 'label' => 'START AN IMPORT'],
            ['key' => 'imports-active', 'label' => 'ACTIVE IMPORTS'],
        ],
    ],
    ['key' => 'exports', 'label' => 'EXPORTS'],
    ['key' => 'files', 'label' => 'FILES'],
    ['key' => 'inventory', 'label' => 'INVENT'],
    ['key' => 'email', 'label' => 'E-MAIL'],
    ['key' => 'chat', 'label' => 'CHAT'],
    ['key' => 'login', 'label' => 'SESSION'],
];

// AB — red station · investigation shell
$treeAb = [
    ['key' => 'files', 'label' => 'FILES'],
    ['key' => 'dossier', 'label' => 'DOSSIER'],
    ['key' => 'email', 'label' => 'E-MAIL'],
    ['key' => 'chat', 'label' => 'CHAT'],
    ['key' => 'login', 'label' => 'SESSION'],
];

// ICU — amber Watchers · shots + files
$treeIcu = [
    ['key' => 'files', 'label' => 'FILES'],
    ['key' => 'shots', 'label' => 'SHOTS'],
    ['key' => 'email', 'label' => 'E-MAIL'],
    ['key' => 'chat', 'label' => 'CHAT'],
    ['key' => 'login', 'label' => 'SESSION'],
];

// RX — bios blue · Oriel · medicine · VEN home · codex lore
$treeRx = [
    ['key' => 'ven', 'label' => 'VEN'],
    ['key' => 'codex', 'label' => 'CODEX'],
    ['key' => 'files', 'label' => 'FILES'],
    ['key' => 'email', 'label' => 'E-MAIL'],
    ['key' => 'chat', 'label' => 'CHAT'],
    ['key' => 'login', 'label' => 'SESSION'],
];

$tree = $treeIo;
$whisper = 'imports · start · active wips · exports';
if ($domL === 'ab') {
    $tree = $treeAb;
    $whisper = 'files · dossier desk · the line is listening';
} elseif ($domL === 'icu') {
    $tree = $treeIcu;
    $whisper = 'files · shot desk · i see you';
} elseif ($domL === 'rx') {
    $tree = $treeRx;
    $whisper = 'ven · codex · codes + lore of the system';
}
?>
<div class="tm-tree">
  <h1 class="tm-page-title flicker">
    <?= htmlspecialchars(defined('SYS_DISPLAY') ? SYS_DISPLAY : (defined('SYS_TAG') ? SYS_TAG : 'TERMINAL')) ?>
  </h1>
  <?php if ($authed): ?>
    <p class="tm-logged">logged in as: <strong><?= htmlspecialchars($agent['display']) ?></strong></p>
  <?php else: ?>
    <p class="tm-logged">logged in as: <em>nobody</em></p>
  <?php endif; ?>

  <nav class="tm-nav-arch">
    <?php if (!$authed || $domL === 'base'): ?>
      <span class="tm-navSec">GATE</span>
      <ul>
        <li class="<?= strtolower($room) === 'login' ? 'is-active' : '' ?>">
          <a href="<?= htmlspecialchars($h('base', 'login')) ?>">LOGIN.EXE</a>
        </li>
      </ul>
      <p class="tm-nav-whisper">station assigned after authenticate</p>
    <?php else: ?>
      <span class="tm-navSec"><?= htmlspecialchars(strtoupper($dom)) ?> DESK</span>
      <ul>
        <?php foreach ($tree as $node):
            $kids = (isset($node['children']) && is_array($node['children'])) ? $node['children'] : [];
            $childKeys = array_map(static fn($c) => strtolower((string) ($c['key'] ?? '')), $kids);
            $roomL = strtolower((string) $room);
            $onSelf = $roomL === strtolower((string) ($node['key'] ?? ''));
            $onChild = $kids && in_array($roomL, $childKeys, true);
            $open = $onSelf || $onChild;
            if ($kids):
                // parent is a drawer label (not a dead end) — first child is default hop
                $first = $kids[0]['key'] ?? $node['key'];
                ?>
          <li class="tm-nav-parent <?= $open ? 'is-open' : '' ?> <?= $onChild || $onSelf ? 'is-active' : '' ?>">
            <a href="<?= htmlspecialchars($h($dom, (string) $first)) ?>"><?= htmlspecialchars((string) $node['label']) ?></a>
            <ul class="tm-nav-sub">
              <?php foreach ($kids as $ch):
                  $chOn = $roomL === strtolower((string) ($ch['key'] ?? ''));
                  ?>
                <li class="<?= $chOn ? 'is-active' : '' ?>">
                  <a href="<?= htmlspecialchars($h($dom, (string) $ch['key'])) ?>"><?= htmlspecialchars((string) $ch['label']) ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
            <?php else:
                $on = $onSelf;
                ?>
          <li class="<?= $on ? 'is-active' : '' ?>">
            <a href="<?= htmlspecialchars($h($dom, (string) $node['key'])) ?>"><?= htmlspecialchars((string) $node['label']) ?></a>
          </li>
            <?php endif; ?>
        <?php endforeach; ?>
      </ul>
      <p class="tm-nav-whisper"><?= htmlspecialchars($whisper, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
  </nav>
</div>
