<?php
// CARL LOVES THE PARSEDOWN PARSER 
require_once 'Parsedown.php';
$Parsedown = new Parsedown();

function render_md($text) {
    static $Parsedown;
    if (!$Parsedown) {
        $Parsedown = new Parsedown();
    }
    return $Parsedown->text($text);
}
?>