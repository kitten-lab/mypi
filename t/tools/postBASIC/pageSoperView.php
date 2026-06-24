<?php 
global $SITE;
global $TOOL;
require_once __DIR__ . '/-SIG-postBASIC.php'; // ASSISTANT SETTINGS
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';
require_once ROUTE_TO_SYSTEMS . 'invokeSky.php';

// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}

$CHEST = ROUTE_TO_LOCALSTORE . DOM_SLUG . '-' . ROOM_SLUG . '.post.json';    
  

if(file_exists($CHEST)) {
    $CHEST_THINGS = json_decode(file_get_contents($CHEST), true);
        $Parsedown = new Parsedown();

        foreach ($CHEST_THINGS as $TIMBER => $contents) {
        $unix = $contents['tps']['ingest_unix'];

        $tpsDT = new DateTime("@$unix");
                $tpsDT->setTimezone(new DateTimeZone("America/New_York"));
                $date = $tpsDT->format('Y-m-d h:i:sa');
                
        echo "<div class='title'><h2>" . $contents['payload']['post']['post_topic'] . "</h2></div>";
        echo "<div class='content'>" . $Parsedown->text($contents['payload']['post']['post_leaf']) . "</div>"; 
        echo "<pre class='content'>" . $date . "</pre>"; 
    } 
} else { 
    echo "No fragments found."; 
    }
?>
