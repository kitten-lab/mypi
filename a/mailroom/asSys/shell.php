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
  <script>
  /* Apply saved skin before paint so O/X (night/day) doesn't flash wrong. */
  (function () {
    try {
      var t = localStorage.getItem('mailroom-theme');
      if (t !== 'light' && t !== 'dark') {
        t = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches
          ? 'light' : 'dark';
      }
      document.documentElement.setAttribute('data-theme', t);
    } catch (e) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  })();
  </script>
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

/* O = night (dark/red) · X = day (light/blue) — uses existing data-theme CSS only */
(function () {
  var KEY = 'mailroom-theme';
  function current() {
    var t = document.documentElement.getAttribute('data-theme');
    return t === 'light' ? 'light' : 'dark';
  }
  function apply(theme) {
    theme = theme === 'light' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', theme);
    document.body.setAttribute('data-theme', theme);
    try { localStorage.setItem(KEY, theme); } catch (e) {}
    var o = document.getElementById('mr-skin-o');
    var x = document.getElementById('mr-skin-x');
    if (o) {
      o.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      o.classList.toggle('is-on', theme === 'dark');
    }
    if (x) {
      x.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
      x.classList.toggle('is-on', theme === 'light');
    }
  }
  apply(current());
  var oBtn = document.getElementById('mr-skin-o');
  var xBtn = document.getElementById('mr-skin-x');
  if (oBtn) oBtn.addEventListener('click', function () { apply('dark'); });
  if (xBtn) xBtn.addEventListener('click', function () { apply('light'); });
})();
console.log('%cmailROOM · O night · X day','background:#1a0a10;color:#e85a6b;padding:4px 8px;font-size:11px');
</script>
<?php setGET('scripts'); ?>
</body>
</html>
