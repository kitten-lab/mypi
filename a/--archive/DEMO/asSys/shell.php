<?php 
global $SITE;
?>

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

<main>

<div class="MAIN">

<?php foreach ($GLOBALS['GETS']['set'] as $fn) {
    echo $fn();
} ?>
</div>
</main>
<?php include 'footer.php'; ?>

</body>
</html>
<!-- AMEN -->
<script>
if (window.location.pathname === '/b/DEMO/' &&
    !window.location.search
) {
  window.location.replace("public/hi-from-SKY");
  }
</script>