<?php

function noKeyFound(){
    skylite(openSky("SKY AUTH FAILURE"));
    skylite(medHeading("keyMAKER Failure Msg: No Key Found"));
    skylite(leaf("Please consider your keys and try again."));
}

function notARoom(){
    skylite(openSky("SKY AUTH FAILURE"));
    skylite(medHeading("keyMAKER Failure Msg: No Room Found"));
    skylite(leaf("Please consider your location. Are you lost?"));
}

function aRoomWithNoKey(){
    skylite(openSky("SKY AUTH FAILURE"));
    skylite(medHeading("keyMAKER Failure Msg: There is a room but no key."));
    skylite(leaf("Are you forgetting something?"));
}

?>