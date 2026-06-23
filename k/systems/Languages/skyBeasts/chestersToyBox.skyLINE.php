<?php 
function chestersToyBox($toy, $DressUp){
  $funName = "CHESTERS TOYBOX";
  $funBox = "t/toys/" . $toy . "/";

  $ToyBox = $GLOBALS['SONAR'] . $funBox;
  $DressUp = strtoupper($DressUp);

if (!is_dir($ToyBox)) {
      KDE_Error_Logger($funName . ": 
" . $toy, " TOY DOES NOT EXIST! CHECK YOUR SPELLING AND TRY AGAIN. 
    " . $ToyBox);
} else {

  $GLOBALS['GETS']['set'][] = function() use ($toy, $ToyBox, $DressUp, $funName, $funBox) {
    $toySet = $ToyBox . $toy . ".SET.php";

    if (is_file($toySet)) {
      include $toySet;
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "  OH NO! THE SET IS MISSING! CALL MOM, MAYBE SHE CAN FIND IT! \n  TELL HER IT WAS SUPPOSED TO BE HERE:" . $funBox);
    }
  };

  $GLOBALS['GETS']['scripts'][] = function() use ($toy, $ToyBox, $funName, $funBox, $DressUp) {
    $PlayScript = $ToyBox . $toy . ".SCRIPT.js";

    if (is_file($PlayScript)) {
      print '<!-- playscript ' . $DressUp . " for toy " . $toy . ': -->
      <script src="' . T_ROUTE . '/toys/' . $toy . "/" . $toy . '.SCRIPT.js"></script>';
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "  OH NO! WE DON'T KNOW WHAT GAME TO PLAY WITH THIS TOY! CALL MOM, I BET SHE CAN HELP. \nTELL HER IT WAS SUPPOSED TO BE HERE:" . $funBox);
    }
  };

  $GLOBALS['GETS']['dressing'][] = function() use ($toy, $ToyBox, $DressUp, $funName,$funBox) {
    $CostumeParty = $ToyBox . $toy . "_" . $DressUp . ".VIZ.css";

    if (is_file($CostumeParty)) {
      print '<!-- toy costume party for ' . $toy . ': -->
     <link rel="stylesheet"  type="text/css" href="' . T_ROUTE . '/toys/' . $toy . '/' . $toy . '_' . $DressUp . '.VIZ.css"></link>';
    } else {
      KDE_Error_Logger($funName . ": " . $toy, "OH NO! THE TOY HAS NO DRESSUPS! WE CAN PLAY THIS WAY, BUT IT MIGHT LOOK WEIRD. THINK ITS WRONG? CALL MOM!  \nTELL HER IT WAS SUPPOSED TO BE HERE:" . $CostumeParty);
    }
  };
}
    
}