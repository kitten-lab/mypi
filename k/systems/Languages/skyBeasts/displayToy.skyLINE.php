<?php 
function displayToy($toy, $DressUp="ClassicBoi"){
  $funName = "CHESTERS TOYBOX";
  $funBox = "t/toys/" . $toy . "/";

  $ToyBox = echoSONAR . $funBox;
  $DressUp = strtoupper($DressUp);

if (!is_dir($ToyBox)) {
      KDE_Error_Logger($funName . ": 
" . $toy, " TOY DOES NOT EXIST! CHECK YOUR SPELLING AND TRY AGAIN. 
    " . $ToyBox);
} else {

  $GLOBALS['GETS']['set'][] = function() use ($toy, $ToyBox, $DressUp, $funName, $funBox) {
    $toySet = $ToyBox . "dressUps/" . $toy . "_" . $DressUp . ".box.php";

    if (is_file($toySet)) {
      include $toySet;
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "  OH NO! THE SET IS MISSING! CALL MOM, MAYBE SHE CAN FIND IT! \n  TELL HER IT WAS SUPPOSED TO BE HERE:" . $toySet);
    }
  };

  $GLOBALS['GETS']['scripts'][] = function() use ($toy, $ToyBox, $funName, $funBox, $DressUp) {
    $PlayScript = $ToyBox . $toy . ".kit.js";

    if (is_file($PlayScript)) {
      // Inline kit so pocket browser / multi-host does not drop playscripts
      $body = file_get_contents($PlayScript);
      print '<!-- playscript ' . htmlspecialchars($DressUp, ENT_QUOTES, 'UTF-8')
        . ' for toy ' . htmlspecialchars($toy, ENT_QUOTES, 'UTF-8') . ' (inline): -->' . "\n"
        . '<script>' . "\n" . $body . "\n" . '</script>' . "\n";
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "  OH NO! WE DON'T KNOW WHAT GAME TO PLAY WITH THIS TOY! CALL MOM, I BET SHE CAN HELP. \nTELL HER IT WAS SUPPOSED TO BE HERE:" . $funBox);
    }
  };


  $GLOBALS['GETS']['dressing'][] = function() use ($toy, $ToyBox, $DressUp, $funName,$funBox) {
    $CostumeParty = $ToyBox . "dressUps/" . $toy . "_" . $DressUp . ".viz.css";

    if (is_file($CostumeParty)) {
      // Inline dress-up CSS (same pocket reason as kits / kittens)
      $baseViz = $ToyBox . $toy . '.viz.css';
      print '<!-- toy costume party for ' . htmlspecialchars($toy, ENT_QUOTES, 'UTF-8') . ' (inline): -->' . "\n<style>\n";
      if (is_file($baseViz)) {
          print file_get_contents($baseViz) . "\n";
      }
      print file_get_contents($CostumeParty) . "\n</style>\n";
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "OH NO! THE TOY HAS NO DRESSUPS! WE CAN PLAY THIS WAY, BUT IT MIGHT LOOK WEIRD. THINK ITS WRONG? CALL MOM!  \nTELL HER IT WAS SUPPOSED TO BE HERE:" . $CostumeParty);
    }
  };
}

    
}