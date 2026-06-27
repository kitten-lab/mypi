<?php 
// kittenCaller function is used for calling small fragments of javascript to the surface
// called "KITTENS" because they are small modular kits for building feature sets. Adorable!

function callKitten(STRING $kitten){

$GLOBALS['GETS']['scripts'][] = function() use ($kitten) {

  $catLocator = echoSONAR;  
  $callKitten = $catLocator . "k";

    $a_WildKitten = "/kittens/" . $kitten . ".kitten.js";
    $hasCollar = "/kittens/" . $kitten . ".collar.php";

    $MEOWMEOW = $callKitten . $a_WildKitten;
    $hasOwner = $callKitten . $hasCollar;
    
  if (is_file($hasOwner)) {
    include $hasOwner;
  } else { 
    error_log("KITTEN HAS NO COLLAR " . $kitten);
  }

  if (is_file($MEOWMEOW)) {
    print '<!-- kitten ' . $kitten . ': -->
      <script src="' . K_ROUTE . $a_WildKitten . '"></script>';
    } else { 
        error_log("MISSING KITTEN! "
         . $kitten
         . ", LAST SEEN: " 
         . $MEOWMEOW);
    }
};
}

?>