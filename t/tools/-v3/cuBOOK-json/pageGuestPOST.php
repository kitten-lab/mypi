<?php 
require_once __DIR__ . '/-SIG-cuBOOK.php'; // ASSISTANT SETTINGS
$cuFIG = getFIG("cuBOOK", "GuestPOST"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


<form method="POST" action="">
<span class="">
    <label for="USER"><?= $cuFIG['UserInput'] ?? "why"; ?></label><br>
    <input 
    rows="1" cols="60"
    name="USER" 
    placeholder="<?= $cuFIG['UserHint']; ?>" 
    required>
    <br>
</span>

<span class="">
    <label for="MESSAGE"><?= $cuFIG['MsgInput']; ?></label><br>
    <input 
    rows="1" cols="60"
    name="MESSAGE" 
    placeholder="<?= $cuFIG['MsgHint']; ?>" 
    required>
    <br>
</span>


  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">
    <?= $cuFIG['Submit_Button'] ?? 'Submit'; ?>
  </button> 
  <button type="reset">Reset Form</button>

  <span>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $cuFIG['Confirmation_Msg'];
    } 
    ?>

    </span>
    </form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
<?php 
$scripts = (string)$GLOBALS['INTERA']['SYSTEM'];
?>
