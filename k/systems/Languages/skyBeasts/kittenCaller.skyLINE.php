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
    // Inline kitten JS so pocket browser / WebView2 still works when host `k`
    // script fetch fails (file:// gate, multi-host quirks, offline kits).
    // External src kept as comment for debugging.
    $body = file_get_contents($MEOWMEOW);
    print '<!-- kitten ' . htmlspecialchars($kitten, ENT_QUOTES, 'UTF-8')
      . ' (inline; also at ' . (defined('K_ROUTE') ? K_ROUTE : 'http://k') . $a_WildKitten . '): -->' . "\n"
      . '<script>' . "\n" . $body . "\n" . '</script>' . "\n";
    } else { 
        error_log("MISSING KITTEN! "
         . $kitten
         . ", LAST SEEN: " 
         . $MEOWMEOW);
    }
};
}

?>