<?php
/* CONFIG — SYS terminal */
$loversMark = 'IOX';

include __DIR__ . '/-FIG--routeErrors.php';
include __DIR__ . '/-FIG--nav.php';

function getMy_Styles()
{
    $sys = defined('SYS_ID') ? SYS_ID : (defined('WORLD_ID') ? WORLD_ID : BLOCK_ID);
    // No pocketChrome here — terminal owns scroll/chrome (CSS garden, not shared SaaS)
    getA_Style('fonts', $sys, 'asSys');
    getA_Style('style', $sys, 'asSys');
    $dom = defined('DOM_SLUG') ? DOM_SLUG : '';
    if ($dom !== '' && $dom !== 'base') {
        getA_Style('skin-' . $dom, $sys, 'asSys');
    } else {
        getA_Style('skin-base', $sys, 'asSys');
    }
}
?>
