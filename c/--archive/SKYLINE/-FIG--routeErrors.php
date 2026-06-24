<?php

function noKeyFound(){
    skylite(openSky("Key Failure"));
    skylite(bigHeading("That isn't a key for this."));
    skylite(leaf("Have you considered not guessing?"));
}

function notARoom(){
        skylite(openSky("SKYLINE FRONT DESK"));
        skylite(bigHeading("SKYLINE REGRETS TO INFORM YOU THAT YOU ARE BETWEEN PLACES"));
        skylite(leaf("
        PERHAPS YOU MEANT TO BE HERE. DID YOU WISH TO VISIT THE PUBLIC_OFFICE?"));
}

function aRoomWithNoKey(){
    skylite(openSky("Room without a Key"));
    skylite(medHeading("There is a room but no key."));
    skylite(leaf("Are you forgetting something?"));
}


?>