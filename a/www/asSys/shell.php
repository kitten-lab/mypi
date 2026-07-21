<?php
/**
 * RESTORED from silo-my-pocket-internet: platform/a/WWW/asSys/shell.php
 * Backup: a/--archive/WWW-master-from-silo/asSys/
 *
 * Browser-window cutie — no site nav chrome. Rooms live only in the inner shell.
 * ($navCall only if something explicitly sets it; we do not inject sideNav.)
 */
setGET('actor');
$SITE = $GLOBALS['SITE'] ?? (defined('BLOCK_ID') ? BLOCK_ID : 'www');
// intentional: do NOT fall back to sideNav/Nav — that printed the room dump
$navCall = $GLOBALS['navCall'] ?? ($GLOBALS[$SITE]['GETS']['navCall'] ?? null);
?>
<!-- insert php actors -->
<!-- BEGIN THE OPENING PRAYER OF PRODUCTION -->
<!DOCTYPE html>
  <html><head>
    <title><?= $GLOBALS[$SITE]['ROOM_DISPLAY'] ?? ($GLOBALS['pageTitle'] ?? 'WWW') ?></title>
    <!-- THE CALLING OF THE STYLESHEET PROCESSION -->
      <!-- insert getMyStyles(); -->
        <?php getMy_Styles() ?>
        <?php setGET("dressing"); ?>
      <!-- insert page generated styles -->
      <style>
        <?php setGET("quickDress"); ?>
      </style>
  </head>
  <body>
  <!-- END OPENING PRAYERS -->

<div class="wwwExplorer_mainShell"></div>

<div class="wwwExplorer_windowTitleBar">
  <?= $GLOBALS['pageTitle'] ?>
</div>

<div class="wwwExplorer_windowToolBar">

<div class="wwwExplorer_linkBar">
  <div role="button" tabindex="0" onclick="WWWBack()" onkeydown="if(event.key==='Enter')WWWBack()">back</div>
  <div role="button" tabindex="0" onclick="WWWForward()" onkeydown="if(event.key==='Enter')WWWForward()">forward</div>
  <span id="wwwBar" class="linkSlug" contenteditable="true" spellcheck="false"></span>
  <div id="GO" role="button" tabindex="0" onclick="LetsGO()" onkeydown="if(event.key==='Enter')LetsGO()">GO!</div>
</div>

<div class="wwwExplorer_innerShell">
  <?php if (!empty($navCall) && file_exists($navCall)) { include $navCall; } ?>

  <main id="browserWindow" class="iox_coreContents">
    <?php setGET("set"); ?>
  </main>
</div>
</div>
  <?php 
    callKitten("siloGreeting");
    callKitten("webBAR");
    callKitten("roomTEXTURE");
  ?>
<script>
(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  if (p === '/' || p === '/www' || p === '/www/index.php') {
    window.location.replace('/www/danyi/index');
  }
})();
console.log("%cLAUNCHING THE SILO.....","background-color:blue;padding:10px;font-weight:600");
</script>
  
    <?php setGET("scripts"); ?>
</body>
</html>
