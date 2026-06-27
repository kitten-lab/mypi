
<?php setGET("actor"); ?>
    <!DOCTYPE html>
    <html><head>
    <title><?= $GLOBALS['pageTitle'] ?></title>
    <!-- THE CALLING OF THE STYLESHEET PROCESSION -->
        <?php getMy_Styles() ?>
        <?php setGET("dressing"); ?>
      <!-- insert page generated styles -->
      <style>
        <?php setGET("quickDress"); ?>
      </style>
  </head>
<body>
<header>
  <?php include 'header.php'; ?>
</header>
<?php setGET("set"); ?>

<footer>
  <?php include 'footer.php'; ?>
</footer>
<script>

if (window.location.pathname === '/requests' &&
    !window.location.search
) {
  window.location.replace("/requests/frontdesk");
  }
console.log("%cLAUNCHING THE SILO.....","background-color:blue;padding:10px;font-weight:600");
</script>
</body>
<?php setGET("script"); ?>

