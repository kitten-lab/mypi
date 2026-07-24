<?php
if (!function_exists('noKeyFound')) {
    function noKeyFound() {
        openSky('MAILROOM');
        medHeading('No bay key');
        leaf('Try /mailroom/floor/sort');
    }
}
if (!function_exists('notARoom')) {
    function notARoom() {
        openSky('MAILROOM');
        medHeading('No bay');
        leaf('Path needs /mailroom/{dom}/{key}');
    }
}
if (!function_exists('aRoomWithNoKey')) {
    function aRoomWithNoKey() {
        openSky('MAILROOM');
        medHeading('Bay without key');
        leaf('Need /mailroom/floor/sort');
    }
}
