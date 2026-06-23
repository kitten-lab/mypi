
<?php 
include __DIR__ . '/../parsedown/Parsedown.php';
require __DIR__ . '/../../systems/rehydrateSelf.php';
require_once __DIR__ . '/../skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);
$ROUTE__LINE = ROUTE('d');

    $TOOL_FUNC = "LOG__POST";
    $TOOL_LOC = "plogBasic";
    $TOOL_NAME = "actorAdd";

$TIUD = $_GET['TUID'];
$GET__MOD = $_GET['MOD'];

$ROUTE = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . $TOOL_LOC . '/' . $sys . '/' . $dom . '/';
$CHEST = $ROUTE . '/' . $GET__MOD . '_data.json';

$CHEST__CONTENTS = json_decode(file_get_contents($CHEST), true);

$Parsedown = new Parsedown();


foreach ($CHEST__CONTENTS as $TIMBER) {
  if ($TIUD == $TIMBER['TUID__REF']) {
    echo "<h1>" . $TIMBER['LOG__LEAF_TOPIC'] . "</h1>";
    echo $Parsedown->text($TIMBER['LOG__LEAF_TEXT']);
  }
}

echo '<br><a href="javascript:history.go(-1)" title="Return to previous page">« Go back</a>';
?>
</div>