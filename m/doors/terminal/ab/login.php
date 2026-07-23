<?php
/**
 * AB session face — logout.
 */
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('ab');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'ab',
    'Terminal AB',
    'login',
    'Login',
    'classic'
);

openSky('login');
h1('session');

getTool('authGATE', 'Logout');

$files = function_exists('mypi_room_href') ? mypi_room_href('ab', 'files') : '/terminal/ab/files';
skylite('<p class="tm-lede tm-lede-quiet"><a href="' . htmlspecialchars($files, ENT_QUOTES, 'UTF-8') . '">files</a></p>');

closeSky();
