<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';
require __DIR__ . '/../skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE

$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);
$config = $GLOBALS['mailroomBasic_List'] ?? [];
$ROUTE__LINE = ROUTE("d");
    $TOOL_LOC = "mailroomBasic";


$ROUTE = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . $TOOL_LOC . '/' . $GLOBALS['sys'];
$CHEST = file_get_contents($ROUTE . '/data.json');
$CHEST_CONTENTS = json_decode($CHEST, true);


$SELECT_TIMBERS = array_filter($CHEST_CONTENTS, function($TIMBER) use ($mod) {
    return $TIMBER['TO__MOD'] === $mod;
});

$SELECT_TIMBERS = array_values($SELECT_TIMBERS);

foreach ($SELECT_TIMBERS as $TIMBER) {
  echo "<span class='mail_listRow'>";
  echo "<span class='mail_listFrom'>FROM: " . $TIMBER['POST__MOD'] . "</span>";
  echo "<span class='mail_listSubject'><a href='window?" . $config['Page_Key'] . "=" . $config['Page_Link'] . '&mail=' . $TIMBER['META_DATA']['UNIX'] . "'>" . $TIMBER['POST__MAIL_TOPIC'] . "</a></span>";
  echo "<span class='mail_listDate'>" . $TIMBER['META_DATA']['GAIA__DATE'] . "</span>";
  echo "</span>";
    
}
?>
