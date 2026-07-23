<?php
if (!function_exists('noKeyFound')) {
    function noKeyFound() {
        openSky('MYTHLEAK FAILURE');
        medHeading('keyMAKER: No Key Found');
        leaf('The juice line has no file for that key. Try /mythleak/news/headlines');
    }
}
if (!function_exists('notARoom')) {
    function notARoom() {
        openSky('MYTHLEAK FAILURE');
        medHeading('keyMAKER: No Room Found');
        leaf('You are not on a mythleak DOM. Start at /mythleak/news/headlines');
    }
}
if (!function_exists('aRoomWithNoKey')) {
    function aRoomWithNoKey() {
        openSky('MYTHLEAK FAILURE');
        medHeading('keyMAKER: Room without key');
        leaf('Path needs /mythleak/{dom}/{key}');
    }
}
