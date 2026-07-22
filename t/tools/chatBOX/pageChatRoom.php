<?php
/**
 * Live chat room — ledger kind=chat + session.
 * Quiet mode: emit window.CHATBOX_BOOT for CBX-001 ROM (no HTML log).
 */
require_once ROUTE_TO_SYSTEMS . 'ledger/Ledger.php';
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php';

$place = mypi_ledger_place_from_sky();
$session = isset($_GET['session']) ? strtolower(trim((string) $_GET['session'])) : 'live';
$session = preg_replace('/[^a-z0-9._-]+/', '-', $session);
if ($session === '') {
    $session = 'live';
}

$err = null;
$rows = [];
$sessions = [];
try {
    $sessions = mypi_ledger_chat_sessions([
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
    ]);
    $rows = mypi_ledger_list([
        'sys' => $place['sys'],
        'dom' => $place['dom'],
        'room' => $place['room'],
        'kind' => 'chat',
        'tool' => 'chatBOX',
        'session' => $session,
        'order' => 'asc',
        'limit' => 200,
    ]);
} catch (Throwable $e) {
    $err = $e->getMessage();
}

// Boot payload for ROM / kitten (always — even when not quiet)
$bootLines = [];
foreach ($rows as $r) {
    $bootLines[] = [
        'c_uid' => $r['c_uid'] ?? '',
        'agent' => $r['agent'] ?? '',
        'body' => $r['body'] ?? '',
        'event_unix' => (int) ($r['event_unix'] ?: $r['ingest_unix']),
        't_uid' => $r['t_uid'] ?? '',
    ];
}
$bootSessions = [];
foreach ($sessions as $s) {
    $bootSessions[] = [
        'session' => $s['session'] ?? 'live',
        'label' => $s['label'] ?? '',
        'n' => (int) ($s['n'] ?? 0),
    ];
}
$boot = [
    'session' => $session,
    'place' => $place,
    'lines' => $bootLines,
    'sessions' => $bootSessions,
    'confirm' => $GLOBALS['CHATBOX_CONFIRM'] ?? null,
    'error' => $err,
];
$bootJson = json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
echo '<script>window.CHATBOX_BOOT = ' . $bootJson . ';</script>' . "\n";
// Easy parse after fetch POST (ROM stays open)
echo '<script type="application/json" id="chatbox-boot-json">' . $bootJson . '</script>' . "\n";

if (!empty($GLOBALS['CHATBOX_QUIET_PAGES'])) {
    return;
}

$Parsedown = new Parsedown();
?>
<section class="chatbox-room">
  <h2 class="chatbox-room-title">
    <?= htmlspecialchars(defined('ROOM_DISPLAY') ? ROOM_DISPLAY : (defined('ROOM_SLUG') ? ROOM_SLUG : 'chat'), ENT_QUOTES, 'UTF-8') ?>
    · session <code><?= htmlspecialchars($session, ENT_QUOTES, 'UTF-8') ?></code>
  </h2>
  <p class="muted">
    <button type="button" onclick="window.location = window.location.pathname + '?session=<?= rawurlencode($session) ?>'">Refresh</button>
    · live hangout (ledger) · oldest → newest
  </p>

  <?php if ($sessions): ?>
    <p class="chatbox-sessions muted">
      Sessions:
      <?php foreach ($sessions as $s):
          $sid = $s['session'] ?? 'live';
          $n = (int) ($s['n'] ?? 0);
          $lab = trim((string) ($s['label'] ?? ''));
          $href = '?session=' . rawurlencode($sid);
      ?>
        <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $sid === $session ? ' class="on"' : '' ?>>
          <?= htmlspecialchars($lab !== '' ? $lab : $sid, ENT_QUOTES, 'UTF-8') ?>
          (<?= $n ?>)
        </a>
      <?php endforeach; ?>
    </p>
  <?php endif; ?>

<?php if ($err): ?>
  <p class="err"><?= htmlspecialchars($err) ?></p>
<?php elseif (!$rows): ?>
  <p class="muted">No lines in this session yet. Say something above.</p>
<?php else: ?>
  <?php foreach ($rows as $r):
      $unix = (int) ($r['event_unix'] ?: $r['ingest_unix']);
      $when = date('D m/d/y h:i:sA', $unix);
      $user = $r['agent'] !== '' ? $r['agent'] : 'anon';
  ?>
    <div class="chat-slug">
      <div class="user-display"><?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="chat-content"><?= $Parsedown->text($r['body'] ?? '') ?></div>
      <pre class="chat-time"><?= htmlspecialchars($when, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($r['c_uid'], ENT_QUOTES, 'UTF-8') ?></pre>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</section>
