<?php require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php';
require_once __DIR__ . '/-SIG-skyGEN.php'; // ASSISTANT SETTINGS

getFIG("skyGEN", "SkyLaunch");
global $mySIGFIG; 
?>

<form method="POST" action="">
<h1>Launch Surface</h1>
<?php wireINPUT("gen-WORLD_SLUG", false, true); ?><br /><br />
<?php wireINPUT("gen-WORLD_DISPLAY", false, true); ?><br /><br />
<?php wireINPUT("gen-DOM_SLUG", false, true); ?><br /><br />
<?php wireINPUT("gen-DOM_DISPLAY", false, true); ?><br /><br />
<?php wireINPUT("gen-MOD_SLUG", false, true); ?><br /><br />
<?php wireINPUT("gen-MOD_DISPLAY", false, true); ?><br /><br />
<?php wireINPUT("gen-KEY_SLUG", false, true); ?><br /><br />
<?php wireINPUT("gen-KEY_DISPLAY", false, true); ?><br /><br />
<?php wireINPUT("gen-URI", false, true); ?><br /><br />
<?php wireINPUT("gen-LOVERS_MARK", false, true); ?><br /><br />

  <input type='hidden' name='POST_SURFACE' value='<?= WORLD_ID ?>'/> 
  <input type='hidden' name='POST_BUILDING' value='<?= DOM_SLUG ?>'/> 
  <input type='hidden' name='POST__MOD' value='<?= MOD_SLUG ?>'/> 
  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">CREATE WORLD</button> 

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo $config['CONFIRM_MSG'] ?? 'WORLD CREATED.';
 } 
 ?>

</form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>