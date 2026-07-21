<?php
/**
 * chatBOX → ledger crates (kind=chat) with **session** meta.
 *
 * Juvenile live tool: each POST is one line in a hangout.
 * Session keeps lines together so many "rooms" can live at one place_path
 * (or one default "live" session per sky room).
 *
 * Crate shape (via mypi_ledger_create_post):
 *   kind:  chat
 *   tool:  chatBOX
 *   agent: display name (username)
 *   body:  message text
 *   topic: short line for lists — "chat · {session} · {user}"
 *   meta:  {
 *     session: string,          // id slug, default "live"
 *     session_label: string,    // human title
 *     place_path: string
 *   }
 *
 * JSON .chat.log.json lives only in -v3/chatBOX-json backup.
 */

function chatBOX_normalize_session(string $raw): string {
    $s = strtolower(trim($raw));
    if ($s === '') {
        return 'live';
    }
    $s = preg_replace('/[^a-z0-9._-]+/', '-', $s);
    $s = trim($s, '-._');
    return $s !== '' ? $s : 'live';
}

/**
 * @return array{ok:bool,c_uid?:string,session?:string,error?:string}
 */
function chatBOX_ledger_store(): array {
    require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
    $place = mypi_ledger_place_from_sky();

    $user = trim((string) ($_POST['username'] ?? $_POST['USER'] ?? 'anon'));
    $message = trim((string) ($_POST['message'] ?? $_POST['MESSAGE'] ?? ''));
    if ($message === '') {
        return ['ok' => false, 'error' => 'empty message'];
    }
    if ($user === '') {
        $user = 'anon';
    }

    $session = chatBOX_normalize_session((string) ($_POST['chat_session'] ?? $_GET['session'] ?? 'live'));
    $sessionLabel = trim((string) ($_POST['chat_session_label'] ?? ''));
    if ($sessionLabel === '') {
        $sessionLabel = $session === 'live'
            ? ('Live · ' . ($place['place_label'] !== '' ? $place['place_label'] : ($place['room'] ?: 'chat')))
            : $session;
    }

    $place_path = trim(implode('/', array_filter([
        $place['sys'], $place['dom'], $place['room'],
    ], 'strlen')), '/');

    $result = mypi_ledger_create_post([
        'topic' => 'chat · ' . $session . ' · ' . $user,
        'body' => $message,
        'agent' => $user,
        'tags_raw' => 'chat,session:' . $session,
        'timezone' => (string) ($_POST['POST__TZ'] ?? ''),
        'event_unix' => $_POST['POST__EVENT_UNIX'] ?? null,
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'mod' => $place['mod'],
        'place_label' => $place['place_label'],
        'tool' => 'chatBOX',
        'tool_version' => 6,
        'kind' => 'chat',
        'actor' => $user,
        'meta' => [
            'session' => $session,
            'session_label' => $sessionLabel,
            'place_path' => $place_path,
            'live' => true,
        ],
    ]);

    if (!empty($result['ok'])) {
        $result['session'] = $session;
    }
    return $result;
}
