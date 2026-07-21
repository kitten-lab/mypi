<?php 
require_once $GLOBALS['INTERA']['SYSTEM'] . 'wireWORDS.php'; // CHEST CRATING SYSTEM
require_once __DIR__ . '/-SIG-cuBOOK.php'; // ASSISTANT SETTINGS

getFIG("cuBOOK", "GuestPOST");
global $mySIGFIG; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


<form method="POST" action="">

<div class="formContainer">
  <?php
  // true = pull placeholder from SIGFIG USER_pl / MESSAGE_pl (or UserHint / MsgHint)
  wireINPUT('USER', true, true);
  wireTEXTAREA('MESSAGE', true, true);
  ?>
</div>

<input type="hidden" name="POST__TZ" id="tz-input">
<input type="hidden"  name="POST__EVENT_UNIX">

<button type="submit"><?= $mySIGFIG['Submit_Button'] ?? 'Submit'; ?></button> 
<button type="reset">Reset Form</button>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
echo $mySIGFIG['Confirmation_Msg'];
} 
?>
</form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
<?php 
$scripts = (string)$GLOBALS['INTERA']['SYSTEM'];
?>
