<?php
/**
 * mailROOM facility shell — owns brand bar, rail chrome, three-zone frame.
 * Tools inject into slots: rail (controls) + set (yard + manage).
 */
setGET('actor');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['authgate_action'] ?? '') === 'logout') {
    if (function_exists('mypi_auth_logout')) {
        mypi_auth_logout();
    }
    header('Location: /mailroom/floor/sort');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($GLOBALS['pageTitle'] ?? 'mailROOM', ENT_QUOTES, 'UTF-8') ?></title>
  <?php getMy_Styles() ?>
  <?php setGET('dressing'); ?>
  <style><?php setGET('quickDress'); ?></style>
</head>
<body class="mailroom-floor pocket-app">
<?php include __DIR__ . '/header.php'; ?>

<div class="tbay" id="mailroom-floor">
  <aside class="tbay-rail" aria-label="mailroom rail">
    <?php /* facility chrome — edit here, not in timberBay */ ?>
    <div class="tbay-rail-head">
      <span class="tbay-rail-mark">MAIL</span>
      <span class="tbay-rail-sub">ROOM</span>
    </div>
    <div class="tbay-rail-tool-slot">
      <?php
      if (!empty($GLOBALS['GETS']['rail']) && is_array($GLOBALS['GETS']['rail'])) {
          foreach ($GLOBALS['GETS']['rail'] as $railFn) {
              if (is_callable($railFn)) {
                  $railFn();
              }
          }
      }
      ?>
    </div>
    <div class="tbay-rail-head tbay-rail-head-2">
      <span class="tbay-rail-mark">CHAR</span>
      <span class="tbay-rail-sub">LIE</span>
    </div>
  </aside>

  <div class="tbay-main-slot">
    <?php setGET('set'); ?>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  if (p === '/' || p === '/mailroom' || p === '/mailroom/index.php') {
    var base = p.indexOf('/mailroom') === 0 ? '/mailroom' : '';
    window.location.replace(base + '/floor/sort');
  }
})();
console.log('%cmailROOM · cosmic facility · tool slots','background:#1a0a10;color:#e85a6b;padding:4px 8px;font-size:11px');
</script>
<?php setGET('scripts'); ?>
</body>
</html>
