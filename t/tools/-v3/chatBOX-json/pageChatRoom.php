<?php 
require_once __DIR__ . '/-SIG-chatBOX.php'; // ASSISTANT SETTINGS
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';
require_once ROUTE_TO_SYSTEMS . 'invokeSky.php';

// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}

echo "<H1>" . ROOM_SLUG . ": " . ROOM_DISPLAY . "</H1>";
    echo '<button type="button" onclick="window.location = window.location.href">Refresh Page</button>';
$CRATE = ROUTE_TO_LOCALSTORE . DOM_SLUG . '-' . ROOM_SLUG . '.chat.log.json';    

if(file_exists($CRATE)) {
    $CRATE_MATERIAL = json_decode(file_get_contents($CRATE), true);
    $CRATE_MATERIAL = array_reverse($CRATE_MATERIAL);

      foreach ($CRATE_MATERIAL as $TIMS => $TIMBER) {
        $Parsedown = new Parsedown();
        echo "<div class='chat-slug'>";
        echo "<div class='user-display'>" . $TIMBER['USER']  . "</div>";
        echo "<div class='chat-content'>" . $Parsedown->text($TIMBER['MESSAGE']) . "</div>"; 
        echo "<pre class='chat-time'>" . date("D m/d/y h:i:sA", $TIMBER['TIME']) . " " .  $TIMBER['cUID'] . "</pre>"; 
        echo "</div>";

    } 
} else { 
    echo "No fragments found."; 
    }
?>
