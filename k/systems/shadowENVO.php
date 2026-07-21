<?php

//SHADOW ENV FUNCTION
function shadowENVO($IS_IT) {
    if ($IS_IT == true) { return '_____/'; }

}

// Tools that never defined SHADOW_TOGGLE still need a default (live not shadow).
if (!defined('SHADOW_TOGGLE')) {
    define('SHADOW_TOGGLE', false);
}

if (!defined('SHADOWENVO')) {
    define('SHADOWENVO', shadowENVO(SHADOW_TOGGLE));
}