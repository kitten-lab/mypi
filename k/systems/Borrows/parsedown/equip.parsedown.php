<?php
// CARL LOVES THE PARSEDOWN PARSER
require_once __DIR__ . '/ParsedownTasks.php';

/**
 * Render markdown → HTML (GFM task lists as read-only checkboxes).
 *
 * Breaks: single newlines → <br> (letter grids, verse, playlists).
 * Spaces: multi-space columns kept (IF    ==MISERY== boards) via
 *   tab→spaces + HTML cleanup so CSS break-spaces won't double-gap.
 * Standard MD would smash those into one line unless you double-newline
 * or wrap ``` — we do not require either for house material.
 */
function render_md($text) {
    static $Parsedown;
    if (!$Parsedown) {
        $Parsedown = new ParsedownTasks();
        $Parsedown->setSafeMode(true);
        $Parsedown->setBreaksEnabled(true);
    }
    $text = (string) $text;
    // Align boards: tabs count as fixed spaces (nbsp-feel under break-spaces)
    $text = str_replace("\t", '    ', $text);
    $html = $Parsedown->text($text);
    // Parsedown emits <br />\n — drop the trailing newline so white-space:
    // break-spaces does not paint a second blank line under each soft break.
    $html = preg_replace('/<br\s*\/?>\s*\n/i', '<br>', $html);
    return $html;
}

/**
 * Configure a Parsedown / ParsedownTasks instance the house way.
 * Use when a page builds its own parser instead of render_md().
 *
 * @param Parsedown $pd
 * @return Parsedown
 */
function mypi_parsedown_configure($pd) {
    if (method_exists($pd, 'setSafeMode')) {
        $pd->setSafeMode(true);
    }
    if (method_exists($pd, 'setBreaksEnabled')) {
        $pd->setBreaksEnabled(true);
    }
    return $pd;
}
