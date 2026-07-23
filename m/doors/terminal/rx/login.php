<?php
require_once __DIR__ . '/../_tm_auth.php';
$agent = tm_require_station('rx');

SKY__AUTH(
    $agent['slug'],
    $agent['display'],
    'rx',
    'Terminal RX',
    'login',
    'Login',
    'classic'
);

openSky('login');
h1('session');

getTool('authGATE', 'Logout');

$ven = function_exists('mypi_room_href') ? mypi_room_href('rx', 'ven') : '/terminal/rx/ven';
skylite('<p class="tm-lede tm-lede-quiet"><a href="' . htmlspecialchars($ven, ENT_QUOTES, 'UTF-8') . '">ven desk</a></p>');

closeSky();
