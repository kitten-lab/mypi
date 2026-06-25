<?php 
require_once ROUTE_TO_SYSTEMS . 'shadowENVO.php';
require_once ROUTE_TO_SYSTEMS . 'invokeSky.php';

// SHADOW ENVIRONMENT SETTINGS AND OVERLAY
$IS_IT = SHADOW_TOGGLE;

if ($IS_IT == true) {
  echo "<div class='sha_env'>shadow mode on</div>";
}
define("AVEN_SLUG", "connection");
$CHEST = ROUTE_TO_CHARLIE . '/by_aven/' . AVEN_SLUG . '.ven.json';    
  

if(file_exists($CHEST)) {
    $CHEST_THINGS = json_decode(file_get_contents($CHEST), true);

    echo"<pre>";

  foreach ($CHEST_THINGS as $TIMBER => $TOMBER) {
  echo "<br>" . $TIMBER . ": ";
  if (is_array($TOMBER)){
    foreach ($TOMBER as $TIM => $TAM){
      if (is_array($TAM)){
        foreach ($TAM as $TOM) {
          echo implode(", ", $TOM);
        }
      } else {
      echo $TAM;
    } 
    }
  }
  echo "</pre>";
  }
} else { 
    echo "No fragments found."; 
    }
?>
