<?php 
require_once __DIR__ . '/-SIG-soprBASIC.php'; // ASSISTANT SETTINGS
require_once ROUTE_TO_SYSTEMS . 'wireWORDS.php'; // CHEST CRATING SYSTEM

getFIG("soprBASIC", "AddFragment");
global $mySIGFIG; 
  $user = 'MRA-' . $mySIGFIG['user'];
  $assistant = 'ADM-' . $mySIGFIG['assistant'];
?>

<!-- Load jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- Wire Form Elements -->
<form method="POST" action="">
 
<?php wireINPUT('soper_section', true, true); ?><br><br>
<?php wireTEXTAREA('soper_leaf'); ?><br><br>
<?php wireINPUT_TAG('POST__TAGS'); ?><br><br>
<?php wireINPUT('POST__EVENT_UNIX'); ?><br><br>
<?php wireAVENACTIVE($user, $assistant); ?>

<input type="hidden" name="POST__TZ" id="tz-input">

<button type="submit">Submit</button> 
<button type="reset">Reset Form</button>

</form>
<!-- end form section -->

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;

  $(function() {
    $("#tagTRACKER").autocomplete({
        source: "getTAGGED.php", // Path to your PHP script
            dataType: "json",
        minLength: 1,
        select: function(event, ui) {
            // Logic to append or replace text in textarea
            console.log("Selected: " + ui.item.value);
        }
    });
  });
</script>

<?php 
  $scripts = (string)ROUTE_TO_SYSTEMS;
  include $scripts . 'NIM/getTAGGED.php';
  include $scripts . 'NIM/localSTORE.php';
?>
