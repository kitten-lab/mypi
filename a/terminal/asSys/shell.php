<?php
setGET('actor');
if (is_file(dirname(__DIR__, 2) . '/_/href_local.php')) {
    require_once dirname(__DIR__, 2) . '/_/href_local.php';
}
require_once (defined('echoSONAR') ? echoSONAR : dirname(__DIR__, 3) . '/') . 'k/puppies/authSession.puppy.php';
mypi_auth_boot();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['authgate_action'] ?? '') === 'logout') {
    mypi_auth_logout();
    $to = function_exists('mypi_room_href') ? mypi_room_href('base', 'login') : '/terminal/base/login';
    header('Location: ' . $to);
    exit;
}

$tmH = function ($d, $k) {
    return function_exists('mypi_room_href') ? mypi_room_href($d, $k) : ('/terminal/' . $d . '/' . $k);
};
$authed = mypi_auth_check();
$agent = mypi_auth_agent();
$homeDom = $authed ? (string) ($agent['dom'] ?: 'io') : 'io';
?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($GLOBALS['pageTitle'] ?? 'TERMINAL') ?></title>
  <?php getMy_Styles() ?>
  <?php setGET('dressing'); ?>
  <style><?php setGET('quickDress'); ?></style>
</head>
<?php
$skin = defined('DOM_SLUG') ? preg_replace('/[^a-z0-9_-]/i', '', DOM_SLUG) : 'base';
if ($skin === '') {
    $skin = 'base';
}
if ($authed && !empty($agent['dom'])) {
    $skin = preg_replace('/[^a-z0-9_-]/i', '', $agent['dom']) ?: $skin;
}
$face = $authed ? strtoupper($agent['slug']) : '???';
?>
<body class="terminal-station terminal-skin-<?= htmlspecialchars($skin) ?>">

<div class="tm-app">
  <?php
  /*
   * Far-left rail = SESSION / LOGOUT strut — not a dup of Files/Email/Chat.
   * Archive energy: >|TERMINAL, Press Start labels, slightly wrong.
   */
  ?>
  <aside class="tm-rail" aria-label="Session rail">
    <div class="tm-rail-brand pywebview-drag-region" data-pocket-drag
         title="drag window · deep: this is your grabber">
      <span class="tm-rail-gt">&gt;|</span>
      <span class="tm-rail-iox">IOX</span>
    </div>
    <?php if ($authed): ?>
      <div class="tm-rail-face" title="you">
        <span class="tm-rail-face-l">U</span>
        <span class="tm-rail-face-n"><?= htmlspecialchars(substr($face, 0, 6)) ?></span>
      </div>
      <form method="post" class="tm-rail-out" title="leave the house">
        <input type="hidden" name="authgate_action" value="logout">
        <button type="submit" class="tm-rail-x">X</button>
        <span class="tm-rail-out-l">OUT</span>
      </form>
      <div class="tm-rail-ghost" aria-hidden="true">put them here</div>
    <?php else: ?>
      <a class="tm-rail-in" href="<?= htmlspecialchars($tmH('base', 'login')) ?>" title="authenticate">
        <span class="tm-rail-in-l">IN</span>
      </a>
      <div class="tm-rail-ghost" aria-hidden="true">who are you</div>
    <?php endif; ?>
    <div class="tm-rail-fill"></div>
    <div class="tm-rail-foot" aria-hidden="true">O·X</div>
  </aside>

  <div class="tm-workspace">
    <aside class="tm-sidebar" aria-label="Station tree">
      <?php include __DIR__ . '/nav.php'; ?>
    </aside>

    <div class="tm-center">
      <div class="tm-broken-top" aria-hidden="true">
        <span class="tm-broken-label">&gt;|TERMINAL</span>
        <span class="tm-broken-whisper">for the collection and protection of your thoughts</span>
      </div>
      <div class="tm-tabline">
        <span class="tm-tab is-on"><?= htmlspecialchars(defined('ROOM_DISPLAY') ? ROOM_DISPLAY : 'screen') ?></span>
        <?php if (defined('DOM_DISPLAY')): ?>
          <span class="tm-tab-path"><?= htmlspecialchars(DOM_DISPLAY) ?><?php if (defined('ROOM_SLUG')): ?> / <?= htmlspecialchars(ROOM_SLUG) ?><?php endif; ?></span>
        <?php endif; ?>
        <span class="tm-tab-agent"><?= htmlspecialchars($authed ? $face : 'NO FACE') ?></span>
      </div>

      <main class="tm-main" id="terminalMain">
        <article class="tm-reading">
          <?php setGET('set'); ?>
        </article>
      </main>
    </div>
  </div>

  <footer class="tm-statusbar">
    <span class="tm-status-brand">CHESTER'S NOW IMPORTING</span>
    <span class="tm-status-sep">|</span>
    <span><?= htmlspecialchars($authed ? strtoupper($agent['dom'] ?: $skin) : 'BASE') ?></span>
    <span class="tm-status-sep">|</span>
    <span><?= htmlspecialchars(defined('ROOM_SLUG') ? ROOM_SLUG : '') ?></span>
    <span class="tm-status-sep">|</span>
    <span class="tm-status-agent"><?= htmlspecialchars($authed ? $agent['display'] : 'NO SESSION') ?></span>
    <span class="tm-status-flex"></span>
    <span class="tm-status-hint"><?= $authed ? 'the forest remembers' : 'log in · put them here' ?></span>
  </footer>
</div>

<?php
  callKitten('siloGreeting');
  callKitten('roomTEXTURE');
?>
<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  if (p === '/' || p === '/terminal' || p === '/terminal/index.php') {
    var base = p.indexOf('/terminal') === 0 ? '/terminal' : '';
    window.location.replace(base + '/base/login');
  }
})();
console.log("%cCONCEPT OF CONNECTION","background:#000;color:#0f0;padding:8px;font-family:monospace");
</script>
<?php setGET('scripts'); ?>
</body>
</html>
