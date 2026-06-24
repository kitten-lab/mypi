<?php 
require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php'; // CHEST CRATING SYSTEM
require_once __DIR__ . '/-SIG-chatBOX.php'; // ASSISTANT SETTINGS

getFIG("chatBOX", "ChatBox");
global $mySIGFIG; 

?>

<!-- Load jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- Wire Form Elements -->
<form method="POST" action="">
 
<?php wireINPUT('username', true, true); ?><br><br>
<?php wireTEXTAREA('message', true, true); ?><br><br>
<input type="hidden" name="POST__TZ" id="tz-input">
<input type="hidden" name="POST__EVENT_UNIX">
<input type="hidden" name="POST__TAGS" value="$chattag">


<button type="submit">Submit</button> 
<button type="reset">Reset Form</button>

</form>
<!-- end form section -->

<?php 
  $scripts = (string)ROUTE_TO_SYSTEMS;
  include $scripts . 'NIM/localSTORE.php';
?>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>