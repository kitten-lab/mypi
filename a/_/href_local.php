<?php
/**
 * Href helper — prefer unified b-front: /{SYS}/{DOM}/{KEY}
 *
 * Local policy (COMMANDCENTER9): always include SYS in path so one host (b) serves all.
 * Dedicated vhosts (starline, book, …) still work if path is only /DOM/KEY — keyMaker strips SYS when present.
 */
if (!function_exists('mypi_room_href')) {
    function mypi_room_href($dom, $key, $sys = null) {
        $dom = trim((string) $dom, '/');
        $key = trim((string) $key, '/');
        $sys = $sys ?? (defined('SYS_ID') ? SYS_ID : (defined('BLOCK_URI') ? BLOCK_URI : (defined('WORLD_ID') ? WORLD_ID : '')));
        $sys = trim((string) $sys, '/');

        // Unified front: always /sys/dom/key when we know sys
        if ($sys !== '') {
            return '/' . rawurlencode($sys) . '/' . rawurlencode($dom) . '/' . rawurlencode($key);
        }
        return '/' . rawurlencode($dom) . '/' . rawurlencode($key);
    }
}

/** Absolute URL on port b (optional host). */
if (!function_exists('mypi_b_url')) {
    function mypi_b_url($path) {
        $path = '/' . ltrim((string) $path, '/');
        $host = getenv('MYPI_B_HOST') ?: 'http://b';
        return rtrim($host, '/') . $path;
    }
}
