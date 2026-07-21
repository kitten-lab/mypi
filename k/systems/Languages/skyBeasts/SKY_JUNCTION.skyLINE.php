<?php

function SKY_JUNCTION($letter){
  global $ENV; 
    $localJUNCTION = 'http://'; //localhost
    $globalJUNCTION = 'imported.to'; //live serving centers
  
  if (function_exists('mypi_env_is_local') ? mypi_env_is_local($ENV ?? '') : in_array($ENV ?? '', ['COMMANDCENTER9', 'ROSEWOOD8', 'LOCAL'], true)) {

    define($letter . '_root', $localJUNCTION . $letter);
    define(strtoupper($letter) . '_ROUTE', $localJUNCTION . $letter);

  } else {

    define($letter . '_root', 'https://' . $letter . "." . $globalJUNCTION);
    define(strtoupper($letter) . '_ROUTE', 'https://' . $letter . "." . $globalJUNCTION);

  }
}

// ROUTER FUNCTIONS


function ROUTE(
  $LETTER, 
  $SHADOW_PROD_TOGGLE
  ){
    return echoSONAR . $SHADOW_PROD_TOGGLE . $LETTER . '/'; 
}

//  simple router without shadow_prod
function ROUTE_LETTER($LETTER){
  return echoSONAR . $LETTER . '/'; 
}