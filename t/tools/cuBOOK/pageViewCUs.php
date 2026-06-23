<?php $SITE = $GLOBALS['SITE'];
global $SONAR;
require_once $SONAR . 't/tools/parsedown/Parsedown.php'; 

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}




$SHADOW_PROD_TOGGLE = $sha_env;
$router_1 = ROUTE('d', $SHADOW_PROD_TOGGLE);

$route = $router_1 . $GLOBALS[$SITE]['URI'] . '/';
    $CHEST = $route . $GLOBALS[$SITE]['DOM_SLUG'] . '-' . $GLOBALS[$SITE]['ROOM_SLUG'] . '.guestcu.json';    
  

if(file_exists($CHEST)) {
    $CHEST_THINGS = json_decode(file_get_contents($CHEST), true);
        $Parsedown = new Parsedown();

        foreach ($CHEST_THINGS as $TIMBER => $contents) {
        $unix = $contents['tps']['ingest_unix'];

        $tpsDT = new DateTime("@$unix");
                $tpsDT->setTimezone(new DateTimeZone("America/New_York"));
                $date = $tpsDT->format('m/d/y h:ia');
        echo "<div class='soper_frag'>";
        echo "<span class='userslug'>User: <strong>" . $contents['payload']['guestCU']['agent'] . "</strong> on " . $date . " says: </span>";
        echo "<span class='cuPOST'>" . $Parsedown->text($contents['payload']['guestCU']['topic']) . "</span>"; 
        echo "</div>"; 
    } 
} else { 
    echo "No fragments found."; 
    }
?>
