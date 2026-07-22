<?php
// CARL LOVES THE PARSEDOWN PARSER
require_once __DIR__ . '/ParsedownTasks.php';

/**
 * Render markdown → HTML (GFM task lists as read-only checkboxes).
 */
function render_md($text) {
    static $Parsedown;
    if (!$Parsedown) {
        $Parsedown = new ParsedownTasks();
        $Parsedown->setSafeMode(true);
    }
    return $Parsedown->text($text);
}
