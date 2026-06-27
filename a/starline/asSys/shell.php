<!-- insert php actors -->
<?php  ?>

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
<body>

<?php include 'header.php'; ?>
<main>

<div class="MAIN">
    <?php setGET("set"); ?>
</div>
</main>
<?php include 'footer.php'; ?>


  <?php 
    callKitten("siloGreeting");
    callKitten("roomTEXTURE");
  ?>
<script>

if (window.location.pathname === '/' &&
    !window.location.search
) {
  window.location.replace("/offices/frontDesk");
  }
console.log("%cLAUNCHING THE SILO.....","background-color:blue;padding:10px;font-weight:600");
</script>
  
    <?php setGET("scripts"); ?>

</body>
</html>
<!-- AMEN -->