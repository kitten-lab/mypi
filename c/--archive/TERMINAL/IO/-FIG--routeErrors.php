<?php

function noKeyFound(){
    skylite(openSky("Key Failure"));
    skylite(bigHeading("That isn't a key for this."));
    skylite(leaf("Have you considered not guessing?"));
}

function notARoom(){
        skylite(openSky("POINT WRONG DIRECTION"));
        skylite(bigHeading("YOU ARE IN A HALLWAY"));
        skylite(leaf("
        THIS SPACE IS THE SPACE BETWEEN SOMEWHERE AND TERMINAL.IO. 
         PERHAPS IT IS THE LAYERS OF THE TERMINAL.IO ITSELF, BETWEEN ITS SEAMS. 
         IT SEEMS YOU ARE TRYING TO MOVE THROUGH THE SCENE. 
         
         I MEAN SEAMS. SEEMS GOOD. PROCEED, SAM."));
}

function aRoomWithNoKey(){
    skylite(openSky("Room without a Key"));
    skylite(medHeading("There is a room but no key."));
    skylite(leaf("Are you forgetting something?"));
}


?>