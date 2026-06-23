<?php $SITE = $GLOBALS['SITE']; 
require_once __DIR__ . '/-SIG-reportBASIC.php'; //GET SHADOW PROD TOGGLE
require_once $GLOBALS['SONAR'] . 't/tools/parsedown/Parsedown.php'; 
require_once $GLOBALS['SONAR'] . 't/tools/skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE

$id = $_GET['id'];
$room = $_GET['w'];

$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);
$ROUTE__LINE = ROUTE('d', $SHADOW_PROD_TOGGLE);


$ROUTE = $ROUTE__LINE . $GLOBALS[$SITE]['URI'] . '/';
  $CHEST = $ROUTE . $GLOBALS[$SITE]['DOM_SLUG'] . '-' . $GLOBALS[$SITE]['ROOM_SLUG'] . '.report.json';
  

$CHEST_THINGS = json_decode(file_get_contents($CHEST), true);

$Parsedown = new Parsedown();

foreach ($CHEST_THINGS as $TIMBER) {
    $content = $TIMBER['payload']['post'];

  if ($id == $TIMBER['tps']['ingest_unix']) {
    echo "<h2>" . $content['topic'] . "</h2>";

    echo $Parsedown->text($content['content']);
  }
}

echo '<br><a href="javascript:history.go(-1)" title="Return to previous page">« Go back</a>';
?>