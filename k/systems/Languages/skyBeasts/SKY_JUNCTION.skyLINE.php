<?php

function SKY_JUNCTION($letter){
  global $ENV; 
    $localJUNCTION = 'http://'; //localhost
    $globalJUNCTION = 'imported.to'; //live serving centers
  
  if ($ENV === "ROSEWOOD8") {

    define($letter . '_root', $localJUNCTION . $letter);
    define(strtoupper($letter) . '_ROUTE', $localJUNCTION . $letter);

  } else {

    define($letter . '_root', 'https://$letter.$globalJUNCTION');
    define(strtoupper($letter) . '_ROUTE', 'https://$letter.$globalJUNCTION');

  }
}

// ROUTER FUNCTIONS


function ROUTE(
  $LETTER, 
  $SHADOW_PROD_TOGGLE
  ){
    return $GLOBALS['SONAR'] . $SHADOW_PROD_TOGGLE . $LETTER . '/'; 
}

//  simple router without shadow_prod
function ROUTE_LETTER($LETTER){
  return $GLOBALS['SONAR'] . $LETTER . '/'; 
}