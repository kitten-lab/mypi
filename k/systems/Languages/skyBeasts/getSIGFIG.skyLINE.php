<?php


function getFIG(
    string $TOOL, 
    string $FUNCTION
  ){
  global $SIGFIG;
  global $mySIGFIG;
    $mySIGFIG = $SIGFIG[$TOOL][$FUNCTION][ROOM_FLAVOR];
}
