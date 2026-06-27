<?php

function noKeyFound(){
    openSky("SKY AUTH FAILURE");
    medHeading("keyMAKER Failure Msg: No Key Found");
    leaf("Please consider your keys and try again.");
}

function notARoom(){
    openSky("SKY AUTH FAILURE");
    medHeading("keyMAKER Failure Msg: No Room Found");
    leaf("Please consider your location. Are you lost?");
}

function aRoomWithNoKey(){
    openSky("SKY AUTH FAILURE");
    medHeading("keyMAKER Failure Msg: There is a room but no key.");
    leaf("Are you forgetting something?");
}

?>