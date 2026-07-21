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

(function () {
  var p = window.location.pathname.replace(/\/+$/, '') || '/';
  // Unified b-front: /starline or /starline/
  if (p === '/' || p === '/starline' || p === '/starline/index.php') {
    var base = p.indexOf('/starline') === 0 ? '/starline' : '';
    window.location.replace(base + '/news/headlines');
  }
})();
console.log("%cLAUNCHING THE SILO.....","background-color:blue;padding:10px;font-weight:600");
</script>
  
    <?php setGET("scripts"); ?>

</body>
</html>
<!-- AMEN -->