<?php 

function /* puppy */ fetchPUPPY($what, $who) {
    define('SKY_AUTH' = 'SKY_AUTH');

    if ($what == SKY_AUTH) {
        return __DIR__ . '/fetchSKY_AUTH-' . $who . '.php';
    }
}