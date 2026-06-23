
<?php require __DIR__ . '/../../systems/rehydrateSelf.php'; ?>
<style>
.form-table {
ddisplay: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr; /* Two equal columns */
  gap: 20px; /* Optional space between columns */ 
  }

.form-row {
display:flex;
align-items: center;
margin-bottom: 8px; }

.form-row label {
width: 150px;
margin-right: 10px;
font-weight: 600;
}

.form-row input, 
.form-row textarea, 
.form-row select {
flex: .5;
}

.form-row pre {
font-size: .6rem;
margin:0}
</style>
<form method="POST" action="">
<div class="form-table">
<div class="form-row">
<label for="SYS_SLUG">WORLD SLUG<pre>(Prime Folder)</pre></label>
<input name="SYS_SLUG" placeholder="{{SYS_SLUG}}" required>
</div>

<div class="form-row">
<label for="SYS_DISPLAY">WORLD NAME<pre>(Display Name)</pre></label>
<input name="SYS_DISPLAY" placeholder="{{SYS_DISPLAY}}" required><br>
</div>

<div class="form-row">
<label for="DOM_SLUG">BUILDING SLUG<pre>(Subfolder)</pre></label>
<input name="DOM_SLUG" placeholder="{{DOM_SLUG}}" required><br>
</div>
<div class="form-row">
<label for="DOM_DISPLAY">BUILDING NAME (primary folder)</label>
<input name="DOM_DISPLAY" placeholder="{{DOM_DISPLAY}}" required><br>
</div>


<div class="form-row">
<label for="MOD_SLUG">MOD Slug (first sub folder)</label>
<input name="MOD_SLUG" placeholder="{{MOD_SLUG}}" required><br>
</div>
<div class="form-row">
<label for="MOD_DISPLAY">MOD Slug (primary folder)</label>
<input name="MOD_DISPLAY" placeholder="{{MOD_DISPLAY}}" required><br>
</div>



<div class="form-row">
<label for="KEY_SLUG">SLUG of First KEY</label>
<input name="KEY_SLUG" placeholder="{{KEY_SLUG}}" required><br>
</div>
<div class="form-row">
<label for="KEY_DISPLAY">First KEY DISPLAY</label>
<input name="KEY_DISPLAY" placeholder="{{KEY_DISPLAY}}" required><br>
</div>


<div class="form-row">
<label for="URI">uri or use sys slug</label>
<input name="URI" placeholder="{{URI}}" required><br>
</div>

<div class="form-row">
<label for="LOVERS_MARK">lovers mark</label>
<input name="LOVERS_MARK" placeholder="{{LOVERS_MARK}}" required><br>
</div>


</div>


  <input type='hidden' name='POST__SYS' value='<?= $sys ?>'/> 
  <input type='hidden' name='POST__DOM' value='<?= $dom ?>'/> 
  <input type='hidden' name='POST__MOD' value='<?= $mod ?>'/> 
  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">CREATE WORLD</button> 

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo $config['CONFIRM_MSG'] ?? 'WORLD CREATED.';
 } 
 ?>

</span>
</form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>