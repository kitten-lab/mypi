
<?php setGET("actor"); ?>
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

  <?php 
    $BLOCK = $GLOBALS[BLOCK_ID]['GETS'];
    if (!empty($BLOCK['Nav']) 
      && file_exists($BLOCK['Nav'])) {
    require $BLOCK['Nav']; 
    } 
  ?>

  <?php setGET("set"); ?>

</body>
<?php setGET("script"); ?>

