<?php

/**
 * cuBOOK crate — guest book storage for pageViewCUs.
 * Path: d/{URI}/{DOM}-{ROOM}.guestcu.json  (TOOL TYPE guestcu)
 */

function json_payload() {
    return [
        'guestCU' => [
            'agent' => $_POST['USER'] ?? ($_POST['username'] ?? 'anon'),
            'topic' => $_POST['MESSAGE'] ?? ($_POST['message'] ?? ''),
        ],
    ];
}

function json_route() {
    $SITE = $GLOBALS['SITE'];
    return [
        'from' => [
            'agent' => $_POST['USER'] ?? ($_POST['username'] ?? 'anon'),
        ],
        'to' => [
            'sys' => $GLOBALS[$SITE]['SYS_SLUG'] ?? '',
            'dom' => $GLOBALS[$SITE]['DOM_SLUG'] ?? (defined('DOM_SLUG') ? DOM_SLUG : ''),
            'mod' => $GLOBALS[$SITE]['MOD_SLUG'] ?? '',
            'key' => $GLOBALS[$SITE]['ROOM_SLUG'] ?? (defined('ROOM_SLUG') ? ROOM_SLUG : ''),
        ],
    ];
}

/**
 * Append a guest entry in the shape pageViewCUs expects.
 */
function cuBOOK_STORE(?string $sha_env = ''): void {
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE] ?? [];

    $shadow = $sha_env ?? '';
    if ($shadow === '' && defined('SHADOW_TOGGLE') && SHADOW_TOGGLE) {
        $shadow = '_____/';
    }

    $uri = $a['URI'] ?? (defined('BLOCK_URI') ? BLOCK_URI : 'www');
    $dom = $a['DOM_SLUG'] ?? (defined('DOM_SLUG') ? DOM_SLUG : 'public');
    $room = $a['ROOM_SLUG'] ?? (defined('ROOM_SLUG') ? ROOM_SLUG : 'home');
    $type = $GLOBALS['TOOL']['TYPE'] ?? 'guestcu';

    $folder = echoSONAR . $shadow . 'd/' . $uri . '/';
    if (function_exists('aleph')) {
        aleph($folder);
    } elseif (!is_dir($folder)) {
        mkdir($folder, 0775, true);
    }

    $path = $folder . $dom . '-' . $room . '.' . $type . '.json';
    $chest = [];
    if (is_file($path)) {
        $chest = json_decode((string) file_get_contents($path), true) ?: [];
    }

    $now = time();
    $cUID = 'cu-' . $now . '-' . bin2hex(random_bytes(3));

    $chest[$cUID] = [
        'tps' => [
            'ingest_unix' => $now,
            'event_unix' => isset($_POST['POST__EVENT_UNIX']) && $_POST['POST__EVENT_UNIX'] !== ''
                ? (int) $_POST['POST__EVENT_UNIX']
                : $now,
        ],
        'payload' => json_payload(),
        'route' => json_route(),
        'tool' => [
            'name' => $GLOBALS['TOOL']['NAME'] ?? 'cuBOOK',
            'function' => $GLOBALS['TOOL']['FUNCTION'] ?? 'GuestPOST',
            'type' => $type,
            'version' => $GLOBALS['TOOL']['VERSION'] ?? 3,
        ],
    ];

    file_put_contents($path, json_encode($chest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
