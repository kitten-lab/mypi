<?php

// the sightsman prepares keys and directs to rooms:

/**
 * Always parse pretty path /DOM/KEY into $_GET[DOM]=KEY.
 * Extra query (?tab=edges) is preserved and does NOT replace path routing.
 */
function keyMaker() {
    global $ENV, $room, $fetch;

    $local = function_exists('mypi_env_is_local')
        ? mypi_env_is_local($ENV ?? '')
        : in_array($ENV ?? '', ['COMMANDCENTER9', 'ROSEWOOD8', 'LOCAL'], true);
    $localSLUG = $local ? '' : (defined('BLOCK_URI') ? BLOCK_URI : '');

    $prettyURI = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($prettyURI === null || $prettyURI === false) {
        $prettyURI = '/';
    }

    if ($localSLUG !== '' && strpos($prettyURI, $localSLUG) !== false) {
        $parsed = trim(str_replace($localSLUG, '', $prettyURI));
    } else {
        $parsed = trim($prettyURI);
    }

    $uriFRAGS = array_values(array_filter(explode('/', $parsed), 'strlen'));
    // /DOM/KEY[/fetch]
    $room = $uriFRAGS[0] ?? null;
    $key = $uriFRAGS[1] ?? null;
    $fetch = $uriFRAGS[2] ?? null;

    if ($room !== null && $room !== '') {
        // Do not wipe other query params (tab, sort, tps, …)
        $_GET[$room] = $key;
    }
}

function lockAndKey() {
    global $SITE;

    $foundKey = false;
    $foundRoom = false;
    $doors = $GLOBALS[$SITE]['tDOM'] ?? [];

    // Prefer DOM keys that match tDOM (ignore tab/sort/tps query noise)
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
        // Fallback: first GET key that matches a door (legacy)
        foreach ($_GET as $room => $key) {
            if (in_array($room, ['tab', 'sort', 'tps', 'here', 'all'], true)) {
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

function interraLocation() {
    // retired, remove from known usage locations
}
