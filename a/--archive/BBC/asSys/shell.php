<?php 
global $SITE; 
$GETS__SITE = $GLOBALS[$SITE]['GETS']; ?>

<?php setGET("actor"); ?>


<!-- .... DEAR INFINITE POTENTIAL, HOLY DOCTYPE... -->
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
<!-- END OPENING PRAYERS -->
<body>

<?php include 'header.php'; ?>
<main>

<div class="NAVIGATION">
<?php 
if (!empty($GETS__SITE['sideNav']) 
    && file_exists($GETS__SITE['sideNav'])) {
  require $GETS__SITE['sideNav']; 
  } 
?>
</div>
<div class="MAIN">
<?php setGET("set"); ?>
</div>
</main>
<?php include 'footer.php'; ?>
  
    <?php setGET("scripts"); ?>
</body>
</html>
<!-- AMEN -->