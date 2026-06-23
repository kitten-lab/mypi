<?php
// fill a set handler 
function GetFILLER($payload, $drop_location){
  $GLOBALS['GETS'][$drop_location][] = function() use ($payload) {
    echo $payload;
  };
}