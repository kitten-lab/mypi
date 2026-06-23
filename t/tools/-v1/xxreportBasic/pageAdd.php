
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

$config = $GLOBALS['-FIG-reportBasic'] ?? []; 
?>
<form method="POST" action="">

    <input 
        name="POST__MOD" 
        placeholder="POST__MOD" 
        required>
    <input 
        name="POST__REPORT_TOPIC" 
        placeholder="POST__REPORT_TOPIC" 
        required>
    <br>
    <textarea 
    rows="10" 
    name="POST__REPORT_TEXT" 
    placeholder="POST__REPORT_TEXT" 
    required>
    </textarea>
    <br>

  <input type='hidden' name='TO__SYS' value='<?= $sys; ?>''/> 
  <input type='hidden' name='TO__DOM' value='<?= $dom; ?>''/> 
  <input type="hidden" name="POST__TZ" id="tz-input">
  <input type='hidden' name='TO__MOD' value='<?= $mod; ?>'/> 
  <input type='hidden' name='POST__REPORT_TYPE' value="<?= $GLOBALS['roomkey']; ?>"/> 

  <button 
    type="submit"
    class="plogBasic_AddButton">
    <?= $config['Submit_Button'] ?? 'Submit'; ?>
  </button> 

  <span class="plogBasic_postMsg">

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $config['Confirmation_Msg'] ?? 'Post accepted.';
    } 
    ?>

    </span>
    </form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
