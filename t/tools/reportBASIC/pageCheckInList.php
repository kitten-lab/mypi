<?php $SITE = $GLOBALS['SITE'];
require_once $GLOBALS['SONAR'] . 't/tools/parsedown/Parsedown.php'; 

require_once __DIR__ . '/-SIG-reportBASIC.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-reportBASIC.php'; // CRATE FILLER SETTINGS

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}
$FIG = getFIG("reportBASIC", "ViewList"); 



$SHADOW_PROD_TOGGLE = $sha_env;
$router_1 = ROUTE('d', $SHADOW_PROD_TOGGLE);

$route = $router_1 . $GLOBALS[$SITE]['URI'] . '/';
    $CHEST = $route . $GLOBALS[$SITE]['DOM_SLUG'] . '-' . $GLOBALS[$SITE]['ROOM_SLUG'] . '.post.json';    
  
 $router_1 . $GLOBALS[$SITE]['SYS_SLUG'] . '/';
    $CHEST = $route . $GLOBALS[$SITE]['DOM_SLUG'] . '-' . $GLOBALS[$SITE]['ROOM_SLUG'] . '.report.json';    
    
if(file_exists($CHEST)) {
$CHEST_THINGS = json_decode(file_get_contents($CHEST), true);
usort($CHEST_THINGS, function($a, $b) {
    return $b['tps']['event_unix'] <=> $a['tps']['event_unix'];
});

foreach ($CHEST_THINGS as $CRATE => $TIMBERS) {
  $unix = $TIMBERS['tps']['event_unix'];
    $tpsDT = new DateTime("@$unix");
            $tpsDT->setTimezone(new DateTimeZone("America/New_York"));
            $date = $unix;
echo "<pre>" . $date . ' | ' . $TIMBERS['payload']['post']['topic'] . "<br>" . $TIMBERS['payload']['post']['content'] . "</pre>";

}
} else {
    echo 'no fragments found';
}

?>
