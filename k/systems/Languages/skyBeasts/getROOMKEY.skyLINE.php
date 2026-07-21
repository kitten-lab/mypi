<?php

// the sightsman prepares keys and directs to rooms:

/**
 * Parse pretty path into $_GET[DOM]=KEY.
 *
 * Supports:
 *   /{DOM}/{KEY}              — dedicated SYS vhost (DocumentRoot = b/{sys})
 *   /{SYS}/{DOM}/{KEY}        — unified port b front (DocumentRoot = b/)
 *
 * Extra query (?tab=edges) preserved; ignored as rooms.
 */
function keyMaker() {
    global $ENV, $room, $fetch;

    $prettyURI = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($prettyURI === null || $prettyURI === false) {
        $prettyURI = '/';
    }

    $uriFRAGS = array_values(array_filter(explode('/', $prettyURI), 'strlen'));

    $sysId = defined('SYS_ID') ? SYS_ID : (defined('BLOCK_URI') ? BLOCK_URI : (defined('WORLD_ID') ? WORLD_ID : ''));
    $sysId = strtolower((string) $sysId);

    // Unified b-front: first segment is this SYS
    if ($sysId !== '' && isset($uriFRAGS[0]) && strtolower($uriFRAGS[0]) === $sysId) {
        array_shift($uriFRAGS);
    }

    // /DOM/KEY[/fetch]
    $room = $uriFRAGS[0] ?? null;
    $key = $uriFRAGS[1] ?? null;
    $fetch = $uriFRAGS[2] ?? null;

    if ($room !== null && $room !== '') {
        $_GET[$room] = $key;
    }
}

function lockAndKey() {
    global $SITE;

    $foundKey = false;
    $foundRoom = false;
    $doors = $GLOBALS[$SITE]['tDOM'] ?? [];
    $ignoreGet = ['tab', 'sort', 'tps', 'here', 'all'];

    foreach ($doors as $door) {
        $dom = $door['DOM'] ?? '';
        if ($dom === '' || !array_key_exists($dom, $_GET)) {
            continue;
        }
        $foundRoom = true;
        $key = $_GET[$dom];
        if ($key === null || $key === '') {
            aRoomWithNoKey();
            require resolveShell();
            exit;
        }
        $path = ROOM_ROUTE . '/' . $dom . '/' . $key . '.php';
        if (file_exists($path)) {
            $foundKey = true;
            require $path;
            break;
        }
        break;
    }

    if (!$foundRoom) {
        foreach ($_GET as $room => $key) {
            if (in_array($room, $ignoreGet, true)) {
                continue;
            }
            foreach ($doors as $door) {
                if ($room == $door['DOM']) {
                    $foundRoom = true;
                    $path = ROOM_ROUTE . '/' . $door['DOM'] . '/' . $key . '.php';
                    if (empty($key)) {
                        aRoomWithNoKey();
                        require resolveShell();
                        exit;
                    }
                    if (file_exists($path)) {
                        $foundKey = true;
                        require $path;
                    }
                    break 2;
                }
            }
        }
    }

    if (!$foundRoom) {
        notARoom();
    }
    if (!$foundKey && $foundRoom) {
        noKeyFound();
    }
    if (!$foundKey && !$foundRoom) {
        noKeyFound();
    }

    require resolveShell();
}

if (!function_exists('notARoom')) {
    function notARoom() {
        if (!isset($GLOBALS['pageTitle'])) {
            $GLOBALS['pageTitle'] = 'No Room Found';
        }
        if (function_exists('openSky')) {
            openSky('No Room Found');
            medHeading('keyMAKER Failure Msg: No Room Found');
            leaf('Please consider your location. Are you lost?');
        }
    }
}

if (!function_exists('noKeyFound')) {
    function noKeyFound() {
        if (!isset($GLOBALS['pageTitle'])) {
            $GLOBALS['pageTitle'] = 'No Key Found';
        }
        if (function_exists('openSky')) {
            openSky('No Key Found');
            medHeading('keyMAKER Failure Msg: No Key Found');
            leaf('Please consider your keys and try again.');
        }
    }
}

if (!function_exists('aRoomWithNoKey')) {
    function aRoomWithNoKey() {
        if (!isset($GLOBALS['pageTitle'])) {
            $GLOBALS['pageTitle'] = 'No Key Found';
        }
        if (function_exists('openSky')) {
            openSky('Room Without Key');
            medHeading('keyMAKER Failure Msg: No Key Found');
            leaf('Path needs /SYS/DOM/KEY on port b (or /DOM/KEY on a dedicated vhost).');
        }
    }
}

if (!function_exists('interraLocation')) {
    function interraLocation() {
        // retired
    }
}
