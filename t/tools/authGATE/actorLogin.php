<?php
/**
 * authGATE · Login actor — POST login / logout.
 */
require_once (defined('echoSONAR') ? echoSONAR : '') . 'k/puppies/authSession.puppy.php';
if (is_file((defined('echoSONAR') ? echoSONAR : '') . 'a/_/href_local.php')) {
    require_once echoSONAR . 'a/_/href_local.php';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    return;
}

mypi_auth_boot();

$action = (string) ($_POST['authgate_action'] ?? 'login');

if ($action === 'logout') {
    mypi_auth_logout();
    $to = function_exists('mypi_room_href')
        ? mypi_room_href('base', 'login')
        : '/terminal/base/login';
    header('Location: ' . $to);
    exit;
}

$user = trim((string) ($_POST['auth_user'] ?? ''));
$pass = (string) ($_POST['auth_pass'] ?? '');
$next = (string) ($_POST['auth_next'] ?? '');

$row = mypi_auth_attempt($user, $pass);
if (!$row) {
    $login = function_exists('mypi_room_href')
        ? mypi_room_href('base', 'login')
        : '/terminal/base/login';
    $sep = strpos($login, '?') !== false ? '&' : '?';
    header('Location: ' . $login . $sep . 'auth_err=' . rawurlencode('Unknown user or keyphrase.'));
    exit;
}

// prefer next if same-origin path under assigned sys/dom
$home = mypi_auth_home_path($row);
if ($next !== '' && isset($next[0]) && $next[0] === '/' && strpos($next, '//') !== 0) {
    $dom = strtolower((string) ($row['dom'] ?? ''));
    $sys = strtolower((string) ($row['sys'] ?? 'terminal'));
    // only honor next if it targets their station
    if ($dom === '' || preg_match('#^/' . preg_quote($sys, '#') . '/' . preg_quote($dom, '#') . '(/|$)#i', $next)) {
        $home = $next;
    }
}

header('Location: ' . $home);
exit;
