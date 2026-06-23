
<?php
function noKeyFound(){
    skylite(openSky("Key Failure"));
    skylite(bigHeading("That isn't a key for this."));
    skylite(leaf("PERHAPS YOU MEANT TO BE HERE. DID YOU WISH TO VISIT THE PUBLIC_OFFICE?"));
}

function notARoom(){
    skylite(openSky("Unauthorized or None Existant Room"));
    skylite(bigHeading("That is not a registered location."));
    skylite(leaf("PERHAPS YOU MEANT TO BE HERE. DID YOU WISH TO VISIT THE PUBLIC_OFFICE?"));
}

function aRoomWithNoKey(){
    skylite(openSky("Room without a Key"));
    skylite(medHeading("There is a room but no key."));
    skylite(leaf("PERHAPS YOU MEANT TO BE HERE. DID YOU WISH TO VISIT THE PUBLIC_OFFICE?"));
}


?>