<?php
// CARL LOVES THE PARSEDOWN PARSER
require_once __DIR__ . '/ParsedownTasks.php';

/**
 * Render markdown → HTML (GFM task lists as read-only checkboxes).
 *
 * Breaks: single newlines → <br> (letter grids, verse, playlists).
 * Multi-space columns: 2+ spaces → &nbsp; runs (no CSS break-spaces pancakes).
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
    $text = str_replace("\t", '    ', $text);
    $html = $Parsedown->text($text);
    // Parsedown emits <br />\n — drop trailing newline after br
    $html = preg_replace('/<br\s*\/?>\s*\n/i', '<br>', $html);
    // Drop empty paragraphs (common gap source under title)
    $html = preg_replace('/<p>\s*<\/p>/i', '', $html);
    $html = preg_replace('/<p>(?:\s|&nbsp;|<br\s*\/?\s*>)*<\/p>/i', '', $html);
    // Preserve multi-space columns without white-space:break-spaces on the whole body
    $html = preg_replace_callback(
        '/>([^<]+)</u',
        static function ($m) {
            $chunk = $m[1];
            // only expand runs of 2+ spaces inside text nodes
            $chunk = preg_replace_callback('/ {2,}/', static function ($s) {
                return str_repeat("\xC2\xA0", strlen($s[0])); // utf-8 nbsp
            }, $chunk);
            return '>' . $chunk . '<';
        },
        $html
    );
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
