<?php 

require_once __DIR__ . '/-SIG-reportBASIC.php'; // ASSISTANT SETTINGS
global $mySIGFIG;
getFIG("reportBASIC", "IntakeReport"); 
$user = 'D-' . $mySIGFIG['user'];
$assistant = 'D-' . $mySIGFIG['assistant'];

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<form method="POST" action="">
<span class="">
    <label for="POST__REPORTER"><?= $mySIGFIG['Reporter']; ?></label>
    <input 
        name="POST__REPORTER" 
        value="<?= $mySIGFIG['Reporter_default']; ?>"
        placeholder="<?= $mySIGFIG['Reporter_plhldr']; ?>" >
</span>
<span class="">
    <label for="POST__TIMBER_TOPIC"><?= $mySIGFIG['Topic']; ?></label>
    <input 
        name="POST__TIMBER_TOPIC" 
        placeholder="<?= $mySIGFIG['Topic_plhldr']; ?>" 
        required>
</span>

<span class="">
    <label for="POST__TIMBER_LEAF"><?= $mySIGFIG['Text']; ?></label><br>
    <textarea 
    rows="2" cols="60"
    name="POST__TIMBER_LEAF" 
    placeholder="<?= $mySIGFIG['Text_plhldr']; ?>" 
    required></textarea>
</span>

<span class="">
    <label for="POST__TAGS"><?= $mySIGFIG['Tags'] ?? 'Tags'; ?></label><br>
    <textarea 
    rows="4" cols="30"
    name="POST__TAGS" id="tag-input" placeholder="type your thread..." /></textarea>
</span>

<span class="">
    <label for="POST__EVENT_UNIX"><?= $mySIGFIG['UNIX']; ?></label><br>
    <input 
        name="POST__EVENT_UNIX" 
        placeholder="<?= $mySIGFIG['UNIX_plhldr']; ?>"
        type="number">
</span>


<label for="agent"><?= $mySIGFIG['Agent'] ?? 'Agent'; ?></label><br>
  <div class="agentRow">
    <label><input type="radio" id="MRA" name="agent" value="<?= $user; ?>"><?= $user; ?></label>
    <label><input type="radio" id="ADM" name="agent" value="<?= $assistant; ?>"><?= $assistant; ?></label>
  </div>

<hr>

<span class="">
  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">
    <?= $mySIGFIG['Submit_Button'] ?? 'Submit'; ?>
  </button> 
  <button type="reset"><?= $mySIGFIG['Reset_Button'] ?? 'Reset'; ?>
</button>
</span>

  <span>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $mySIGFIG['Confirmation_Msg'];
    } 
    ?>

    </span>
    </form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
<?php 
$scripts = (string)$GLOBALS['INTERA']['SYSTEM'];
include $scripts . 'NIM/DEMOgetTAGGED.php';
include $scripts . 'NIM/DEMOlocalSTORE.php';
?>
