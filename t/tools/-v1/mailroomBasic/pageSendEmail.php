
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

$config = $GLOBALS['mailroomBasic'] ?? []; 
 ?>

<div class="blogBasic_container">
<h1>
    <?= $config['addSectTitle'] ?? 'Echo-Traceback'; ?>
</h1>
<p class="blogBasic_content">
    <?= $config['addSectText'] ?? 'Send an Echo'; ?>
</p>


<form method="POST" action="">

    <input name="TO__MOD"
        placeholder="TO__MOD" 
        required>

    <input name="TO__DOM" 
        placeholder="TO__DOM" 
        required>
        

    <input name="TO__SYS"
        placeholder="TO__SYS" 
        required>

    <input name="POST__MAIL_TOPIC" 
        placeholder="POST__MAIL_TOPIC" 
        required>
        
    <textarea name="POST__MAIL_TEXT" 
    placeholder="POST__MAIL_TEXT" 
    required></textarea>
  

  <input type='hidden' name='POST__SYS' value='<?php echo "$sys";?>'/> 
  <input type='hidden' name='POST__DOM' value='<?php echo "$dom";?>'/> 
  <input type='hidden' name='POST__MOD' value='<?php echo "$mod";?>'/> 
  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">Submit</button> 

<span class="blogBasic_postMsg">

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo $config['confirmMsg'] ?? 'Post accepted.';
 } 
 ?>

</span>
</form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>