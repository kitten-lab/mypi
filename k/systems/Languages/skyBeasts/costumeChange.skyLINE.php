<?php

function costumeChange($attribute, $styles){
  $result = " ." . $attribute . ' {' . $styles . "} ";
    getFILLER($result, "quickDress");
}

function quickDressing($attribute, $styles){
  costumeChange($attribute, $styles);
}

