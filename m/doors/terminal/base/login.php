<?php
/**
 * Terminal · BASE · login — CHESTER'S IMPORTS TERMINAL NETWORK
 * Public gate. Station assigned by account.
 */

require_once echoSONAR . 'k/puppies/authSession.puppy.php';
if (is_file(echoSONAR . 'a/_/href_local.php')) {
    require_once echoSONAR . 'a/_/href_local.php';
}
mypi_auth_boot();

if (
    mypi_auth_check()
    && empty($_GET['auth_err'])
    && ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST'
    && empty($_GET['stay'])
) {
    header('Location: ' . mypi_auth_home_path());
    exit;
}

SKY__AUTH(
/* mod */  'switchboard', 'SWITCHBOARD',
/* dom */  'base', 'Base station',
/* room */ 'login', 'Login',
/* texture */ 'classic'
);

openSky('login');
h1("CHESTER'S IMPORTS");
skylite('<p class="tm-lede" style="font-size:1.25rem;letter-spacing:0.06em;text-transform:uppercase;opacity:0.9">TERMINAL NETWORK</p>');

// gate copy stays sparse — shell already speaks (forest / put them here)
getTool('authGATE', 'Login');

closeSky();
