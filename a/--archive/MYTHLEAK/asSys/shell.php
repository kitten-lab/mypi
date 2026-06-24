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