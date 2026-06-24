<?php

function noKeyFound(){
    skylite(openSky("Key Failure"));
    skylite(bigHeading("That isn't a key for this."));
    skylite(leaf("Have you considered not guessing?"));
}
function notARoom(){
        skylite(openSky("LOST"));
        skylite(bigHeading("That isn't a room in this house."));
        skylite(leaf("Have you considered not guessing?"));
}

function aRoomWithNoKey(){
    skylite(openSky("Room without a Key"));
    skylite(medHeading("There is a room but no key."));
    skylite(leaf("Are you forgetting something?"));
}


?>