<?php
setGET('actor');
$SITE = $GLOBALS['SITE'] ?? (defined('BLOCK_ID') ? BLOCK_ID : 'mythleak');
?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($GLOBALS['pageTitle'] ?? 'MYTHLEAK', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <?php
  // shared pocket scroll tokens (optional; safe if missing)
  $pc = (defined('echoSONAR') ? echoSONAR : '') . 'a/_/cssSlugs/pocketChrome.css';
  if (is_file($pc)) {
      echo '<style>' . file_get_contents($pc) . '</style>';
  }
  getMy_Styles();
  ?>
  <?php setGET('dressing'); ?>
  <style><?php setGET('quickDress'); ?></style>
</head>
<body class="mythleak-station pocket-app">
<!-- DEAR INFINITE POTENTIAL, HOLY DOCTYPE — AMEN -->

<div class="ml-shell">
<?php include __DIR__ . '/header.php'; ?>

<div class="ml-app">
  <?php include __DIR__ . '/nav.php'; ?>
  <main class="ml-main MAIN pocket-scroll" id="mythleakMain">
    <?php setGET('set'); ?>
  </main>
  <aside class="ml-ads" aria-label="ads">
    <div class="ml-ad-block">
      <p class="ml-ad-title">!! SPONSORED !!</p>
      <p class="ml-ad-body">SUPPLEMENTARY ACTS-OF-GOD INSURANCE<br>your local agent already knows</p>
    </div>
    <div class="ml-ad-block ml-ad-glitch">
      <p class="ml-ad-title">OUAVA NETWORK</p>
      <p class="ml-ad-body">tips: marrow@imported.to<br>staff only: the juice line</p>
    </div>
  </aside>
</div>

<?php include __DIR__ . '/footer.php'; ?>
</div>

<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  if (p === '/' || p === '/mythleak' || p === '/mythleak/index.php' || p === '/index.php') {
    var base = p.indexOf('/mythleak') === 0 ? '/mythleak' : '';
    window.location.replace(base + '/news/headlines');
  }
})();
console.log("%cMYTHLEAK — THE GODS ARE REAL","background:red;color:white;padding:8px 12px;font-family:monospace");
</script>
<?php setGET('scripts'); ?>
</body>
</html>
