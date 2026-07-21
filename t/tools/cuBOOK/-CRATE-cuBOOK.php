<?php
/**
 * cuBOOK → ledger crates (kind=guestcu).
 * JSON guestcu path lives in -v3/cuBOOK-json backup only.
 */

function cuBOOK_place(): array {
    require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
    return mypi_ledger_place_from_sky();
}

/**
 * @return array{ok:bool,c_uid?:string,error?:string}
 */
function cuBOOK_ledger_store(): array {
    require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
    $place = cuBOOK_place();
    $agent = trim((string) ($_POST['USER'] ?? $_POST['username'] ?? 'anon'));
    $body = trim((string) ($_POST['MESSAGE'] ?? $_POST['message'] ?? ''));
    if ($agent === '' && $body === '') {
        return ['ok' => false, 'error' => 'empty guest entry'];
    }
    return mypi_ledger_create_post([
        'topic' => $agent !== '' ? ('guest · ' . $agent) : 'guest book',
        'body' => $body,
        'agent' => $agent !== '' ? $agent : 'anon',
        'tags_raw' => (string) ($_POST['POST__TAGS'] ?? ''),
        'timezone' => (string) ($_POST['POST__TZ'] ?? ''),
        'event_unix' => $_POST['POST__EVENT_UNIX'] ?? null,
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'mod' => $place['mod'],
        'place_label' => $place['place_label'],
        'tool' => 'cuBOOK',
        'tool_version' => 6,
        'kind' => 'guestcu',
        'actor' => $place['mod'] !== '' ? $place['mod'] : 'guest',
        'meta' => [
            'payload' => 'guestcu',
            'agent' => $agent,
        ],
    ]);
}
