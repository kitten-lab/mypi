<?php 

$FIG = getFIG("JUKEBOX", "UploadSong"); 

?>

<form method="POST" action="">
<span class="">
    <label for="UPLOADER"><?= $FIG['Uploader'] ?? "Uploader"; ?></label>
    <input 
        name="UPLOADER" 
        placeholder="<?= $FIG['Topic_plhldr']; ?>" 
        required>
    <br>
    <br>
</span>
<span class="">
    <label for="artist"><?= $FIG['Text'] ?? "Artist Name"; ?></label>
    <input 
        name="artist" 
        placeholder="<?= $FIG['Topic_plhldr']; ?>" 
        required>
    <br>
</span>
<span class="">
    <label for="song_title"><?= $FIG['Text'] ?? "Artist Name"; ?></label>
    <input 
        name="song_title" 
        placeholder="<?= $FIG['Topic_plhldr']; ?>" 
        required>
    <br>
</span>
<span class="">
    <label for="link"><?= $FIG['Text'] ?? "link to song"; ?></label>
    <input 
        name="link" 
        placeholder="<?= $FIG['Topic_plhldr']; ?>" 
        required>
    <br>
</span>
    <input 
        name="POST__TAGS" 
        placeholder="Tags">
    <br>
<span class="">
    <label for="POST__EVENT_UNIX"><?= $FIG['UNIX']; ?></label><br>
    <input 
        name="POST__EVENT_UNIX" 
        placeholder="<?= $FIG['UNIX_plhldr']; ?>"
        type="number">
    <br>
</span>

  <input type="hidden" name="POST__TZ" id="tz-input">

  <button type="submit">
    <?= $FIG['Submit_Button'] ?? 'Submit'; ?>
  </button> 

  <span>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $FIG['Confirmation_Msg'];
    } 
    ?>

    </span>
    </form>

<script>
  document.getElementById('tz-input').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>
