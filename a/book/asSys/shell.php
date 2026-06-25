
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
  <div class="flex-container">
<div class="flex-child">
  <?php 
    
function navINSERTER(?string $section = null){
  // Define your directory path and file extension pattern
  $directory = ROUTE_LETTER('m') . "/doors/" . BLOCK_URI . "/" . $section . '/*.php'; 

  // Loop through all files matching the pattern
  echo "<h3>" . $section . "</h3>";
  echo "<ul>";
  foreach (glob($directory) as $file) {
    $fileName = basename($file, '.php'); // Strip extension
    echo "<li><a href='/" . $section . "/" . $fileName . "'>" . $fileName . "</a></li>";
  }
  echo "</ul>";
}
navINSERTER("fragments");
navINSERTER("terminal_girls");

  ?>
</div>
<div class="flex-child">
  <?php setGET("set"); ?>
</div>
  </div>

</body>
<?php setGET("script"); ?>

