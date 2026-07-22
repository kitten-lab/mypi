<!-- insert php actors -->
<?php setGET("actor"); ?>
<!-- BEGIN THE OPENING PRAYER OF PRODUCTION -->
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($GLOBALS['pageTitle'] ?? 'Book') ?></title>
  <?php getMy_Styles() ?>
  <?php setGET("dressing"); ?>
  <style>
    <?php setGET("quickDress"); ?>
  </style>
</head>
<body class="book-atelier pocket-app">
<!-- END OPENING PRAYERS -->

<?php include __DIR__ . '/header.php'; ?>

<div class="bk-layout">
  <aside class="bk-sidebar" aria-label="Book pages">
    <?php include __DIR__ . '/nav.php'; ?>
  </aside>

  <main class="bk-main" id="bookMain">
    <article class="bk-page MAIN">
      <?php setGET("set"); ?>
    </article>
    <?php include __DIR__ . '/footer.php'; ?>
  </main>
</div>

<?php
  callKitten("siloGreeting");
  callKitten("webBAR");
  callKitten("roomTEXTURE");
?>
<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  if (p === '/' || p === '/book' || p === '/book/index.php') {
    var base = p.indexOf('/book') === 0 ? '/book' : '';
    window.location.replace(base + '/fragments/connection');
  }
})();
console.log("%cBOOK ATELIER — parchment open","background:#2a1810;color:#e8d4a8;padding:8px 12px;");
</script>
<?php setGET("scripts"); ?>
</body>
</html>
<!-- AMEN -->
