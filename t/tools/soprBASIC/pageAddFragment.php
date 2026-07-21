<?php
require_once __DIR__ . '/-SIG-soprBASIC.php';
require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php';

getFIG('soprBASIC', 'AddFragment');
global $mySIGFIG;
$user = $mySIGFIG['user'] ?? 'user';
$assistant = $mySIGFIG['assistant'] ?? 'assistant';
?>
<form method="POST" action="" class="sopr-form">
  <?php wireINPUT('soper_section', true, true); ?><br>
  <?php wireTEXTAREA('soper_leaf', true, true); ?><br>
  <?php wireINPUT_TAG('POST__TAGS', true, false); ?><br>
  <?php wireINPUT('POST__EVENT_UNIX', true, false); ?><br>
  <?php wireAVENACTIVE($user, $assistant); ?>
  <input type="hidden" name="POST__TZ" id="tz-input">
  <button type="submit"><?= htmlspecialchars($mySIGFIG['Submit_Button'] ?? 'Store', ENT_QUOTES, 'UTF-8') ?></button>
  <button type="reset">Reset</button>
  <?php if (!empty($GLOBALS['SOPR_CONFIRM'])): ?>
    <p class="sopr-confirm"><strong><?= htmlspecialchars($GLOBALS['SOPR_CONFIRM'], ENT_QUOTES, 'UTF-8') ?></strong></p>
  <?php endif; ?>
</form>
<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
