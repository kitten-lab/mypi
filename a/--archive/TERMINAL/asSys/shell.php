
<!-- insert php actors -->
<?php setGET("actor"); ?>
<!-- BEGIN THE OPENING PRAYER OF PRODUCTION -->
<!DOCTYPE html>
  <html><head>
    <title><?= $GLOBALS['pageTitle'] ?></title>
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

<div class="monitor-container">
<div class="monitor-interior">
<div class="screen-content">
<div class="iox_coreContainer">

<?php 
if (!empty($GLOBALS[$SITE]['GETS']['navCall']) && file_exists($GLOBALS[$SITE]['GETS']['navCall'])) {
    require $GLOBALS[$SITE]['GETS']['navCall']; 
    } 
    ?>

<main class="iox_coreContents">
<div class="broken_header">
</div>
    <?php setGET("set"); ?>



</main>
</div></div></div>

<!-- END NOW THE 'BODY OF THE DIVINE PAGE' -->
</div>
<div class="computer_scene">
  <div class="computer_cube">
    <div class="computer_face front"></div>
    <div class="computer_face top">O</div>
    <div class="computer_face pole">X</div>
    <div class="computer_face pole2">O</div>
  </div>
</div>
  <?php 
    callKitten("webBAR");
    callKitten("roomTEXTURE");
  ?>
  
    <?php setGET("scripts"); ?>

</body>
</html>
<!-- AMEN -->