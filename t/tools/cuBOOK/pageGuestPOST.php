<?php
require_once __DIR__ . '/-SIG-cuBOOK.php';
require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php';

getFIG('cuBOOK', 'GuestPOST');
global $mySIGFIG;
?>
<form method="POST" action="" class="cubook-form">
  <div class="formContainer">
    <?php wireINPUT('USER', true, true); ?>
    <?php wireTEXTAREA('MESSAGE', true, true); ?>
  </div>
  <input type="hidden" name="POST__TZ" id="tz-input">
  <input type="hidden" name="POST__EVENT_UNIX">
  <button type="submit"><?= htmlspecialchars($mySIGFIG['Submit_Button'] ?? 'Submit', ENT_QUOTES, 'UTF-8') ?></button>
  <button type="reset">Reset</button>
  <?php if (!empty($GLOBALS['CUBOOK_CONFIRM'])): ?>
    <p class="cubook-confirm"><strong><?= htmlspecialchars($GLOBALS['CUBOOK_CONFIRM'], ENT_QUOTES, 'UTF-8') ?></strong></p>
  <?php endif; ?>
</form>
<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
