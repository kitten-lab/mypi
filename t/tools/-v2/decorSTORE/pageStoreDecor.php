<?php require_once $GLOBALS['INTERA']['SYSTEM'] . 'wireWORDS.php'; 
$SITE = $GLOBALS['SITE'];
$decorSTORE = $GLOBALS['SONAR'] . "m/decor/" . $GLOBALS[$SITE]['URI'] . "/";

echo $decorSTORE;

?>

<form method="POST" enctype="multipart/form-data">
<?php wireFILEinput("fileToUpload","Your file",true); ?>
<?php wireINPUT("filename","Your filename (include filetype)",true); ?>
<?php wireINPUT("directory","Your directory",true); ?>

<input type="submit" value="Upload File">
</form>