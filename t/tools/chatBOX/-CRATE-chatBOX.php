<?php
/**
 * chatBOX → ledger crates (kind=chat) with **session** meta.
 *
 * Juvenile live tool: each POST is one **line** in a hangout (not a whole session blob).
 *
 * Field map (do not conflate speaker with topic):
 *   agent  = who said it (nick / username)     ← user lives HERE only
 *   body   = what they said (message text)
 *   topic  = hangout label for generic lists   ← session title, NOT the user
 *   kind   = chat
 *   tool   = chatBOX
 *   meta   = {
 *     session: string,        // id slug, default "live"  ← hangout key lives HERE
 *     session_label: string,
 *     place_path: string,
 *     live: true,
 *     speaker: string
 *   }
 *   tags_raw = ONLY intentional Charlie material (user-supplied).
 *              Never session ids, place paths, or tool filters — those are payload/columns.
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

/** Charlie person tag from nick — @rosey, not stuffed into topic. */
function chatBOX_speaker_tag(string $user): string {
    $u = strtolower(trim($user));
    $u = ltrim($u, '@');
    $u = preg_replace('/[^a-z0-9._-]+/', '-', $u);
    $u = trim($u, '-._');
    if ($u === '') {
        $u = 'anon';
    }
    return '@' . $u;
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

    // Charlie: nick as @person (production tag). Session stays in meta only.
    // Optional extra threading from the form merges after the speaker tag.
    $nickTag = chatBOX_speaker_tag($user);
    $extra = trim((string) ($_POST['POST__TAGS'] ?? $_POST['tags'] ?? ''));
    $tags_raw = $nickTag;
    if ($extra !== '') {
        $tags_raw .= ',' . $extra;
    }

    $result = mypi_ledger_create_post([
        // topic = hangout name for crate browsers — never the speaker
        'topic' => $sessionLabel,
        'body' => $message,
        'agent' => $user,
        'tags_raw' => $tags_raw,
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
            // explicit so readers never mine topic for nick
            'speaker' => $user,
        ],
    ]);

    if (!empty($result['ok'])) {
        $result['session'] = $session;
    }
    return $result;
}
