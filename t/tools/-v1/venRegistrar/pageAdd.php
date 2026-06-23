
<?php 
require __DIR__ . '/../../systems/rehydrateSelf.php';

$config = $venRegistrar ?? []; 

?>

<div class="venRegistrar_commonBox">
<form method="POST" action="">
<span class="formLabel wide"><?= date('\RY \E\Dm:\E\Tw:\E\Nd') ?> 
<select name="cwEPC" id="cwEPC">
  <option value="EPO7.GAIA">EPO7.GAIA</option>
</select> </span><br>
<span class="formLabel">ven.ID:</span>

<input name="VEN1"
    placeholder="VEN1" maxlength="3" size="3"
    required>-<input name="VEN2"maxlength="3" size="3"
    placeholder="VEN2"
    required> 
<select name="keyType" id="keyType">
  <option value="__UNDEFINED__">Unset Classifier</option>
  <option value="LOCATION">Place</option>
  <option value="OBJECT">Thing</option>
  <option value="AGENT">Person</option>
</select><br>
<span class="formLabel">keyLabel:</span>
<span><input name="keyLabel" placeholder="Reference Name(discreet but memorable)" size="40"></span><br>
<span class="formLabel">keyName:</span>
<input name="keyName" placeholder="In-World Names(usable)" size="40"><br>
<span class="formLabel" >scrubName:</span>
<input name="scrubName" placeholder="True names used in logs(scrub later)" size="40"><br>
<textarea name="registryNote" placeholder="Notes for forest.source (if applicable)" size="40" cols="53" rows="5"></textarea>
    <br>
  <input type='hidden' name='betSys' value='<?= $sys ?>'/> 
  <input type="hidden" name="betTZone" id="tz-input">
  <input type='hidden' name='betDom' value='<?= $dom ?>'/> 
  <input type='hidden' name='betMod' value='<?= $mod ?>'/> 
  <button type="submit">Send to source</button> 
</div>
<span class="venRegistrar_confirmMsg">

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo $config['confirmMsg'] ?? 'Post accepted.';
 } 
 ?>

</span>
</form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>