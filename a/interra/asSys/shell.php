<!-- insert php actors -->
<?php setGET("actor"); ?>
<!-- BEGIN THE OPENING PRAYER OF PRODUCTION -->
<!DOCTYPE html>
  <html><head>
    <title><?= $GLOBALS[BLOCK_ID]['ROOM_DISPLAY'] ?></title>
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
  <div onclick="WWWBack()">back</div>
  <div onclick="WWWForward()">forward</div> 
  <span id="wwwBar" class="linkSlug" contenteditable="true"></span>
  <div id="GO" onclick="LetsGO()">GO!</div>
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
<script> console.log("%cLAUNCHING THE SILO.....","background-color:blue;padding:10px;font-weight:600"); </script>
  
    <?php setGET("scripts"); ?>
</body>
</html>