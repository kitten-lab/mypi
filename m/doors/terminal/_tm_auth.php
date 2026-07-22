<?php
/**
 * Shared gate for terminal doors (except base/login).
 * Sets $tm_agent for SKY__AUTH.
 */
if (!defined('echoSONAR')) {
    // doors load after AUTH which defines echoSONAR
}
require_once echoSONAR . 'k/puppies/authSession.puppy.php';
if (is_file(echoSONAR . 'a/_/href_local.php')) {
    require_once echoSONAR . 'a/_/href_local.php';
}

mypi_auth_boot();

/**
 * @param string $dom required station (io, …)
 * @return array{slug:string,display:string,dom:string,sys:string}
 */
function tm_require_station(string $dom): array
{
    mypi_auth_require([
        'sys' => 'terminal',
        'dom' => $dom,
        'redirect' => function_exists('mypi_room_href')
            ? mypi_room_href('base', 'login')
            : '/terminal/base/login',
    ]);
    return mypi_auth_agent();
}
