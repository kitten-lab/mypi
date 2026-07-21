<?php
/**
 * Local pretty href helper — SYS/DOM/ROOM path without double SYS.
 * Use when building nav links on COMMANDCENTER9 vhosts.
 *
 * Local:  /{DOM}/{KEY}
 * Public: /{SYS}/{DOM}/{KEY}  (when not mypi_LOCAL)
 */
if (!function_exists('mypi_room_href')) {
    function mypi_room_href($dom, $key, $sys = null) {
        $dom = trim((string) $dom, '/');
        $key = trim((string) $key, '/');
        $local = defined('mypi_LOCAL') ? mypi_LOCAL : true;
        if ($local) {
            return '/' . rawurlencode($dom) . '/' . rawurlencode($key);
        }
        $sys = $sys ?? (defined('BLOCK_URI') ? BLOCK_URI : (defined('WORLD_ID') ? WORLD_ID : ''));
        $sys = trim((string) $sys, '/');
        if ($sys === '') {
            return '/' . rawurlencode($dom) . '/' . rawurlencode($key);
        }
        return '/' . rawurlencode($sys) . '/' . rawurlencode($dom) . '/' . rawurlencode($key);
    }
}
