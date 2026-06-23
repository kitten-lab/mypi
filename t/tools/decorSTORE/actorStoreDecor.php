<?php
$SITE = $GLOBALS['SITE'];

function aleph($ROUTE){
    // plow the field is there is no directory, so space can exist //
    if (!is_dir($ROUTE)) { mkdir($ROUTE, 0777, true); }
}

$decorSTORE = $GLOBALS['SONAR'] . "m/decor/" . $GLOBALS[$SITE]['URI'] . "/";
$targetDir = $_POST['directory'];
$decorName = $_POST['filename'];

$storageUnit = $decorSTORE . $targetDir;
aleph($storageUnit);

$targetFile = $storageUnit . "/" . basename($_FILES["fileToUpload"]["name"]);

if (move_uploaded_file($decorName, $targetFile)) {
    echo "The file has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
}

?>