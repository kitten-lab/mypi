<?php $GETS__SITE = $GLOBALS[$SITE]['GETS']; ?>
<?php foreach ($GLOBALS['GETS']['actor'] as $fn) 
    { echo $fn(); } 

$SITE = $GLOBALS['SITE'];
?>

<!-- .... DEAR INFINITE POTENTIAL, HOLY DOCTYPE... -->
<!DOCTYPE html>
<html><head>

<?php 
foreach ($GLOBALS['GETS']['dressing'] as $fn) {
    echo $fn();
} ?>
<?php getMy_Styles(); ?>
<title><?= $GLOBALS['pageTitle'] ?></title>

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
if (window.location.pathname === '/imported-to/' &&
    !window.location.search
) {
  window.location.replace("/imported-to?public=hi-from-SKY");
  }
</script>