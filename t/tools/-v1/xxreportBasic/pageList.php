<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';
require_once __DIR__ . '/../skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);
$ROUTE__LINE = ROUTE('d');

    $TOOL_FUNC = "LOG__POST";
    $TOOL_LOC = "plogBasic";
    $TOOL_NAME = "actorAdd";

$ROUTE = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . $TOOL_LOC . '/' . $sys . '/' . $dom . '/';
$CHEST = $ROUTE . '/' . $mod . '_data.json';

$logs = json_decode(file_get_contents($CHEST), true);

if (!$logs) {
  $logs = [];
}

$config = $GLOBALS['plogBasicList'] ?? []; 

foreach ($logs as $log) {
    $cUID = $log[0];
  echo "<span><a href='window?" . $config['Page_Key'] . "=" . $config['Page_Link'] . '&TUID=' . $log['TUID__REF'] . "&MOD=" . $mod . "'>";
  echo $log['LOG__LEAF_TOPIC'] . "</a> ";
    echo "<span class='plogBasic_metaData'>";
    echo $log['META_DATA']['GAIA_DATE'];
    echo "</span>";
  echo "</span>";
}
?>
