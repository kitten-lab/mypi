<?php
// Error faces: prefer getROOMKEY.skyLINE defaults if already loaded.
if (!function_exists('noKeyFound')) {
    function noKeyFound() {
        openSky('SKY AUTH FAILURE');
        medHeading('keyMAKER Failure Msg: No Key Found');
        leaf('Please consider your keys and try again.');
    }
}
if (!function_exists('notARoom')) {
    function notARoom() {
        openSky('SKY AUTH FAILURE');
        medHeading('keyMAKER Failure Msg: No Room Found');
        leaf('Please consider your location. Are you lost?');
    }
}
if (!function_exists('aRoomWithNoKey')) {
    function aRoomWithNoKey() {
        openSky('SKY AUTH FAILURE');
        medHeading('keyMAKER Failure Msg: There is a room but no key.');
        leaf('Are you forgetting something?');
    }
}
