<?php



function setGET($setitem) {
  if(!empty($GLOBALS['GETS'][$setitem])) {
    foreach ($GLOBALS['GETS'][$setitem] as $fn) { 
      echo $fn(); 
    }
  };
}
