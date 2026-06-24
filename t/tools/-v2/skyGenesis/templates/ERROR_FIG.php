
<?php
function noKeyFound(){
    skylite(openSky("Key Failure"));
    skylite(bigHeading("That isn't a key for this."));
    skylite(leaf("404 Key Not Found"));
}

function notARoom(){
    skylite(openSky("Unauthorized or None Existant Room"));
    skylite(bigHeading("That is not a registered location."));
    skylite(leaf("404 Room Not Found"));
}

function aRoomWithNoKey(){
    skylite(openSky("Room without a Key"));
    skylite(medHeading("There is a room but no key."));
    skylite(leaf("404 Key Not Found for Room"));
}


?>