<?php 
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';

require_once __DIR__ . '/-SIG-soprBASIC.php'; // ASSISTANT SETTINGS
require_once __DIR__ . '/-CRATE-soprBASIC.php'; // CRATE FILLER SETTINGS


// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}

$CHEST = ROUTE_TO_LOCALSTORE . DOM_SLUG . '-' . ROOM_SLUG . '.sopr.frags.json';    
  


if(file_exists($CHEST)) {
    $CHEST_THINGS = json_decode(file_get_contents($CHEST), true);
        $Parsedown = new Parsedown();

foreach ($CHEST_THINGS as $CRATE) {
  foreach ($CRATE as $TIMBER) {
            echo "<h3>" . $TIMBER['LABEL'] . "</h3>";
    foreach ($TIMBER['SOPERS'] as $SOPR){
        echo $Parsedown->text($SOPR['FRAG']);
                
    }          
  } 
}
} else { 
    echo "No fragments found."; 
    }
