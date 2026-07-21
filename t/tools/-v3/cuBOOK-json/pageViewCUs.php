<?php $SITE = $GLOBALS['SITE'];
global $SONAR;
// Parsedown lives with system Borrows (same as soprBASIC/chatBOX) — not t/tools/parsedown
require_once ROUTE_TO_SYSTEMS . 'Borrows/parsedown/Parsedown.php'; 

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}




$SHADOW_PROD_TOGGLE = is_string($sha_env) ? $sha_env : '';
$router_1 = ROUTE('d', $SHADOW_PROD_TOGGLE);

$uri = $GLOBALS[$SITE]['URI'] ?? (defined('BLOCK_URI') ? BLOCK_URI : 'www');
$dom = $GLOBALS[$SITE]['DOM_SLUG'] ?? (defined('DOM_SLUG') ? DOM_SLUG : 'danyi');
$room = $GLOBALS[$SITE]['ROOM_SLUG'] ?? (defined('ROOM_SLUG') ? ROOM_SLUG : 'index');
$route = $router_1 . $uri . '/';
$CHEST = $route . $dom . '-' . $room . '.guestcu.json';

if (file_exists($CHEST)) {
    $CHEST_THINGS = json_decode((string) file_get_contents($CHEST), true) ?: [];
    $Parsedown = new Parsedown();

    // newest first
    $CHEST_THINGS = array_reverse($CHEST_THINGS, true);

    foreach ($CHEST_THINGS as $TIMBER => $contents) {
        if (!is_array($contents)) {
            continue;
        }
        $unix = $contents['tps']['ingest_unix'] ?? time();
        $tpsDT = new DateTime('@' . $unix);
        $tpsDT->setTimezone(new DateTimeZone('America/New_York'));
        $date = $tpsDT->format('m/d/y h:ia');
        $agent = $contents['payload']['guestCU']['agent'] ?? '?';
        $topic = $contents['payload']['guestCU']['topic'] ?? '';
        echo "<div class='soper_frag'>";
        echo "<span class='userslug'>User: <strong>" . htmlspecialchars($agent, ENT_QUOTES, 'UTF-8')
            . '</strong> on ' . $date . ' says: </span>';
        echo "<span class='cuPOST'>" . $Parsedown->text($topic) . '</span>';
        echo '</div>';
    }
} else {
    echo 'No fragments found.';
}
?>
