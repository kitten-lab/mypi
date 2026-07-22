<!-- insert php actors -->
<?php  ?>

<?php setGET("actor"); ?>
<!-- BEGIN THE OPENING PRAYER OF PRODUCTION -->
<!DOCTYPE html>
  <html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($GLOBALS['pageTitle'] ?? 'STARLINE') ?></title>
    <!-- THE CALLING OF THE STYLESHEET PROCESSION -->
      <!-- insert getMyStyles(); -->
        <?php getMy_Styles() ?>
        <?php setGET("dressing"); ?>
      <!-- insert page generated styles -->
      <style>
        <?php setGET("quickDress"); ?>
      </style>
  </head>
  <body class="starline-citadel pocket-app">
  <!-- END OPENING PRAYERS -->

<?php include __DIR__ . '/header.php'; ?>

<main class="sl-main pocket-scroll" id="starlineMain">
  <div class="MAIN sl-panel">
    <?php setGET("set"); ?>
  </div>
  <?php include __DIR__ . '/footer.php'; ?>
</main>

  <?php
    callKitten("siloGreeting");
    callKitten("webBAR");
    callKitten("roomTEXTURE");
  ?>
<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  // Unified b-front: /starline or /starline/
  if (p === '/' || p === '/starline' || p === '/starline/index.php') {
    var base = p.indexOf('/starline') === 0 ? '/starline' : '';
    window.location.replace(base + '/news/headlines');
  }
})();
console.log("%cSTARLINE CITADEL ONLINE","background:linear-gradient(90deg,#1a1f3a,#3d2a5c);color:#c8d4ff;padding:8px 12px;font-weight:600");
</script>

    <?php setGET("scripts"); ?>

</body>
</html>
<!-- AMEN -->
