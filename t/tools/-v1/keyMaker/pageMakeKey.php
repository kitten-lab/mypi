
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

$config = $GLOBALS['keyMaker_MakeKey'] ?? []; 
?>
<form method="POST" action="">

    <input 
        name="GEN__KEY_OPENSKY" 
        class="GEN__KEY_OPENSKY" 
        placeholder="GEN__KEY_OPENSKY"  
        required>
    <br>

    <input 
    class="GEN__KEY_FOR_HOUSE" 
    name="GEN__KEY_FOR_HOUSE" 
    placeholder="GEN__KEY_FOR_HOUSE" 
        required>
    <br>

    <input 
    class="GEN__KEY_FOR_ROOM" 
    name="GEN__KEY_FOR_ROOM" 
    placeholder="GEN__KEY_FOR_ROOM" 
        required>
    <br>


    <input 
    class="GEN__KEY_NAME" 
    name="GEN__KEY_NAME" 
    placeholder="GEN__KEY_NAME" 
        required>
    <br>

    <textarea 
    rows="10" cols="40" 
        name="GEN__KEY_SKYBODY" 
        class="GEN__KEY_SKYBODY" 
        placeholder="GEN__KEY_SKYBODY" 
    required>
    </textarea>
    <br>

  <button 
    type="submit"
    class="plogBasic_AddButton">
    <?= $config['Submit_Button'] ?? 'Submit'; ?>
  </button> 

  <input type='hidden' name='POST__SYS' value='<?= $sys ?>'/> 
  <input type='hidden' name='POST__DOM' value='<?= $dom ?>'/> 
  <input type='hidden' name='POST__MOD' value='<?= $mod ?>'/> 
  <input type="hidden" name="POST__TZ" id="tz-input">

  <span class="plogBasic_postMsg">

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $config['Confirmation_Msg'] ?? 'Post accepted.';
    } 
    ?>

    </span></div>
    </form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
