<?php
require_once $GLOBALS['INTERA']['TOOLS'] . 'parsedown/Parsedown.php'; 

$md = file_get_contents(__DIR__ . '/../../../z/import/detective-mystery-slot.md');
$md = utf8_decode($md);
        $Parsedown = new Parsedown();
    echo $Parsedown->text($md);


?>
