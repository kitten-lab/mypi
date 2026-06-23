
<?php 
require_once __DIR__ . '/-SIG-keyMAKER2.php'; // ASSISTANT SETTINGS
require_once $GLOBALS['INTERA']['SYSTEM'] . 'wireWORDS.php'; // CHEST CRATING SYSTEM
require __DIR__ . '/../../systems/rehydrateSelf.php';

$FIG = getFIG("keyMAKER2", "MakeKey"); 

?>
<form method="POST" action="">
<?php 

$formELEMTS = [
    "input" => [
      [ "sys SLUG", "The system name or combo URI", true ],
      [ "dom SLUG", "The subfolder to place the file in", true ],
      [ "room SLUG", "The SLUG of the dom (subfolder)", true ],
      [ "mod SLUG", "You name goes here!", true ],
      [ "dom DISPLAY", "The DISPLAY name for the DOM", true ],
      [ "room DISPLAY", "the DISPLAY name for the KEY", true ],
      [ "mod DISPLAY", "You name goes here!", true ],
    ],
    "textarea" => [
        [ "skyBODY", "The body of your page", false]
    ]
];

echo "<div class='formContainer'>";

foreach ($formELEMTS as $hm => $els){
    foreach ($els as $el) {
    if ($hm == "input"){
        wireINPUT($el[0],$el[1],$el[3]);
    }
    if ($hm == "textarea"){
        wireTEXTAREA($el[0],$el[1],$el[3]);
    }
    }
    
}
echo "</div>";
?>




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
