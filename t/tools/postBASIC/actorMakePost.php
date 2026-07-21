<?php
/**
 * postBASIC MakePost → mypi ledger (SQLite), not room .post.json slips.
 */
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

require_once __DIR__ . '/-SIG-postBASIC.php';
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$topic = trim((string) (
    $_POST['post_topic']
    ?? $_POST['POST__TIMBER_TOPIC']
    ?? $_POST['POST__TOPIC']
    ?? ''
));
$body = trim((string) (
    $_POST['post_leaf']
    ?? $_POST['POST__TIMBER_LEAF']
    ?? $_POST['POST__LEAF']
    ?? $_POST['content']
    ?? ''
));
$tags_raw = (string) ($_POST['POST__TAGS'] ?? $_POST['tags'] ?? '');
$agent = (string) ($_POST['agent'] ?? 'user');
$tz = (string) ($_POST['POST__TZ'] ?? '');
$event = $_POST['POST__EVENT_UNIX'] ?? $_POST['post_event_unix'] ?? '';

$sys = defined('WORLD_ID') ? WORLD_ID : (defined('SYS_ID') ? SYS_ID : (string) ($GLOBALS['SITE'] ?? ''));
$dom = defined('DOM_SLUG') ? DOM_SLUG : '';
$room = defined('ROOM_SLUG') ? ROOM_SLUG : '';
$mod = defined('MOD_SLUG') ? MOD_SLUG : '';
$place_label = defined('ROOM_DISPLAY') ? ROOM_DISPLAY : '';

$result = mypi_ledger_create_post([
    'topic' => $topic,
    'body' => $body,
    'tags_raw' => $tags_raw,
    'agent' => $agent,
    'timezone' => $tz,
    'event_unix' => $event === '' ? null : $event,
    'sys' => $sys,
    'dom' => $dom,
    'room' => $room,
    'mod' => $mod,
    'place_label' => $place_label,
    'tool' => 'postBASIC',
    'tool_version' => 5,
    'kind' => 'post',
    'actor' => $mod !== '' ? $mod : 'hands',
]);

$GLOBALS['POSTBASIC_LAST'] = $result;
if (!empty($result['ok'])) {
    // Soft confirm for page layer
    $GLOBALS['POSTBASIC_CONFIRM'] = 'HEADLINE STORED · ' . $result['c_uid'];
} else {
    $GLOBALS['POSTBASIC_CONFIRM'] = 'STORE FAILED · ' . ($result['error'] ?? 'unknown');
}
