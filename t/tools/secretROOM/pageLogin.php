<?php
require_once $GLOBALS['INTERA']['SYSTEM'] . 'wireWORDS.php'; // CHEST CRATING SYSTEM

echo '<form method="POST">';
wireINPUT("skyAUTH","Enter Password");
echo "<button type='submit' onsubmit='setTimeout(() => { window.location.reload(); }, 10)'>ENTER</button>";
echo "</form>";


