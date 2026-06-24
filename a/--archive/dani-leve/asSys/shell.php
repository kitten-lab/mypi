<?php 
global $SITE; 
$GETS__SITE = $GLOBALS[$SITE]['GETS']; 
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
<body class="theme-<?= $GLOBALS['ROOM_FLAVOR']; ?>">
<?php include 'header.php'; ?>

<main>

<div class="MAIN">
    <?php setGET("set"); ?>
</div>
</main>
<?php include 'footer.php'; ?>

<script>
if (window.location.pathname === '/dani-leve/' &&
    !window.location.search
) {
  window.location.replace("/dani-leve?portfolio=home");
  }
</script>
    <?php setGET("scripts"); ?>

</body>
</html>
<!-- AMEN -->