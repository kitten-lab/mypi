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

// IO — full green house
$treeIo = [
    ['key' => 'import', 'label' => 'IMPORT'],
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

$tree = $treeIo;
$whisper = 'import · exports · use the rail to leave';
if ($domL === 'ab') {
    $tree = $treeAb;
    $whisper = 'files · dossier desk · the line is listening';
} elseif ($domL === 'icu') {
    $tree = $treeIcu;
    $whisper = 'files · shot desk · i see you';
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
            $on = (strtolower($room) === strtolower($node['key']));
            ?>
          <li class="<?= $on ? 'is-active' : '' ?>">
            <a href="<?= htmlspecialchars($h($dom, $node['key'])) ?>"><?= htmlspecialchars($node['label']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="tm-nav-whisper"><?= htmlspecialchars($whisper, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
  </nav>
</div>
