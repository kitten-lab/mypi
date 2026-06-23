
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

$config = $GLOBALS['plogBasicAdd'] ?? []; 
?>
<form method="POST" action="">

    <input 
        name="POST__LOG_TOPIC" 
        placeholder="POST__LOG_TOPIC" 
        required>
    <br>
    <textarea 
    rows="10" 
    name="POST__LOG_TEXT" 
    placeholder="POST__LOG_TEXT" 
    required>
    </textarea>
    <br>

  <input type='hidden' name='POST__SYS' value='<?= $sys; ?>''/> 
  <input type='hidden' name='POST__DOM' value='<?= $dom; ?>''/> 
  <input type="hidden" name="POST__TZ" id="tz-input">
  <input type='hidden' name='POST__MOD' value='<?= $mod; ?>'/> 

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
