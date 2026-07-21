<?php
require_once __DIR__ . '/-SIG-chatBOX.php';
require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php';

getFIG('chatBOX', 'ChatBox');
global $mySIGFIG;

// stick to active session from query when posting
$activeSession = isset($_GET['session']) ? preg_replace('/[^a-z0-9._-]+/i', '-', (string) $_GET['session']) : 'live';
if ($activeSession === '') {
    $activeSession = 'live';
}
?>
<form method="POST" action="" class="chatbox-form">
  <?php wireINPUT('username', true, true); ?><br>
  <?php wireTEXTAREA('message', true, true); ?><br>
  <?php wireINPUT('chat_session', true, false); ?><br>
  <?php wireINPUT('chat_session_label', true, false); ?><br>
  <input type="hidden" name="POST__TZ" id="tz-input">
  <input type="hidden" name="POST__EVENT_UNIX">
  <button type="submit">Say it</button>
  <button type="reset">Reset</button>
  <?php if (!empty($GLOBALS['CHATBOX_CONFIRM'])): ?>
    <p class="chatbox-confirm"><strong><?= htmlspecialchars($GLOBALS['CHATBOX_CONFIRM'], ENT_QUOTES, 'UTF-8') ?></strong></p>
  <?php endif; ?>
</form>
<script>
(function () {
  var tz = document.getElementById('tz-input');
  if (tz) tz.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
  var sess = document.querySelector('input[name="chat_session"]');
  if (sess && !sess.value) sess.value = <?= json_encode($activeSession) ?>;
})();
</script>
