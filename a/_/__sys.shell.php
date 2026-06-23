
    <!DOCTYPE html>
    <html><head>
    <title><?= $GLOBALS['pageTitle'] ?></title>

<?php foreach ($GLOBALS['GETS']['set'] as $fn) { echo $fn(); } ?>