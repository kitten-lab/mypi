<?php $SITE = $GLOBALS['SITE'];

require_once $GLOBALS['INTERA']['TOOLS'] . 'parsedown/Parsedown.php'; 

require_once __DIR__ . '/-SIG-postBASIC.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-postBASIC.php'; // CRATE FILLER SETTINGS

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}

$FIG = getFIG("postBasic", "ViewList"); 



$SHADOW_PROD_TOGGLE = $sha_env;
$router_1 = ROUTE('d', $SHADOW_PROD_TOGGLE);

$route = $router_1 . $GLOBALS[$SITE]['URI'] . '/';
    $CHEST = $route . $GLOBALS[$SITE]['DOM_SLUG'] . '-' . $GLOBALS[$SITE]['ROOM_SLUG'] . '.post.json';    
  

if(file_exists($CHEST)) {
$TRUNK = json_decode(file_get_contents($CHEST), true);

$PAGE = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($PAGE < 1) $PAGE = 1;

$perPAGE = 3;
$total = count($TRUNK);
$totalPAGES = ceil($total / $perPAGE);

$offset = ($PAGE - 1) * $perPAGE;
$CHEST_THINGS = array_slice($TRUNK, $offset, $perPAGE);

usort($CHEST_THINGS, function($a, $b) {
    return $b['tps']['event_unix'] <=> $a['tps']['event_unix'];
});

foreach ($CHEST_THINGS as $TIMBER => $contents) {
  $unix = $contents['tps']['ingest_unix'];

    $tpsDT = new DateTime("@$unix");
            $tpsDT->setTimezone(new DateTimeZone("America/New_York"));
            $date = $tpsDT->format('Y-m-d h:i:sa');
  echo "<div><h3>";
  echo $contents['payload']['post']['topic'];
  echo "</h3>";
  echo "<pre>";
  echo "<a href='?w=" . $GLOBALS[$SITE]['ROOM_SLUG'] . '&id=' . $unix . "'>read more</a>";
  echo "<br>";
  echo "posted by " . substr($contents['payload']['post']['agent'], 4);
  echo "</pre>";
  echo "<hr>";
  echo "</div>";
}


    if ($PAGE > 1) {
        echo '<a href="#&page=' . ($PAGE - 1) . '">Prev</a>';
    }
    if ($PAGE < $totalPAGES) {
        echo '<a href="?&page=' . ($PAGE + 1) . '">Next</a>';
    }

} else { 
    echo "No fragments found."; 
    }


?>
