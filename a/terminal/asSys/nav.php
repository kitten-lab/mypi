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

$h = function ($d, $key) {
    return function_exists('mypi_room_href')
        ? mypi_room_href($d, $key)
        : ('/terminal/' . $d . '/' . $key);
};

$treeIo = [
    ['key' => 'import', 'label' => 'IMPORT'],
    ['key' => 'exports', 'label' => 'EXPORTS'],
    ['key' => 'files', 'label' => 'FILES'],
    ['key' => 'inventory', 'label' => 'INVENT'],
    ['key' => 'email', 'label' => 'E-MAIL'],
    ['key' => 'chat', 'label' => 'CHAT'],
    ['key' => 'login', 'label' => 'SESSION'],
];
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
    <?php if (!$authed || strtolower($dom) === 'base'): ?>
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
        <?php foreach ($treeIo as $node):
            $on = (strtolower($room) === strtolower($node['key']));
            ?>
          <li class="<?= $on ? 'is-active' : '' ?>">
            <a href="<?= htmlspecialchars($h($dom, $node['key'])) ?>"><?= htmlspecialchars($node['label']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="tm-nav-whisper">import · exports · use the rail to leave</p>
    <?php endif; ?>
  </nav>
</div>
