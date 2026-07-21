<?php
/* Restored load list from c/--archive/WWW/--SIG--WWW.php (master WWW styles) */
$loversMark = '808ʞps';

include __DIR__ . '/-FIG--routeErrors.php';
include __DIR__ . '/-FIG--nav.php';

function getMy_Styles() {
    $SITE = $GLOBALS['SITE'];
    $sys = $GLOBALS[$SITE]['SYS'] ?? (defined('BLOCK_ID') ? BLOCK_ID : 'www');
    getA_Style('style', $sys, 'asSys');
    getA_Style('sky', $sys, 'asSys');
    getA_Style('fonts', $sys, 'asSys');
    // optional per-DOM dress if present
    if (!empty($GLOBALS[$SITE]['DOM'])) {
        getA_Style('style', $GLOBALS[$SITE]['DOM'], 'asDom');
    }
    getA_Style('shadowENVO', '_', 'cssSlugs');
}
