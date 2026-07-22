<?php
/**
 * Parsedown + GFM task lists (visual checkboxes, not interactive).
 *
 *   - [ ] open item
 *   - [x] done item
 *   1. [X] also works on ordered lists
 *
 * Renders as disabled <input type="checkbox"> so safeMode stays happy
 * (AST elements, not raw HTML).
 */
require_once __DIR__ . '/Parsedown.php';

class ParsedownTasks extends Parsedown
{
    /**
     * @param array<int, string> $lines
     * @return array<int, array>
     */
    protected function li($lines)
    {
        $checked = null;
        if (isset($lines[0]) && is_string($lines[0])) {
            // GFM: [ ] / [x] / [X] immediately after the list marker text
            if (preg_match('/^\[([ xX])\](?:[ \t]+(.*))?$/s', $lines[0], $m)) {
                $checked = (strtolower($m[1]) === 'x');
                $lines[0] = array_key_exists(2, $m) ? $m[2] : '';
            }
        }

        $Elements = parent::li($lines);

        if ($checked === null) {
            return $Elements;
        }

        $box = array(
            'name' => 'input',
            'attributes' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled',
                'class' => 'md-task-checkbox',
                'aria-hidden' => 'true',
            ),
        );
        if ($checked) {
            $box['attributes']['checked'] = 'checked';
        }

        array_unshift($Elements, $box, array('text' => ' '));

        return $Elements;
    }
}
