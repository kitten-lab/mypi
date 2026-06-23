<?php
require_once $GLOBALS['INTERA']['SYSTEM'] . 'rehydrateSelf.php';
require_once $GLOBALS['INTERA']['SYSTEM'] . 'chestersCrates.php'; //GET SHADOW PROD TOGGLE
require_once $GLOBALS['INTERA']['TOOLS'] . 'skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
require_once __DIR__ . '/-SIG-JUKEBOX.php'; //GET SHADOW PROD TOGGLE
require_once __DIR__ . '/-CRATE-JUKEBOX.php'; //GET SHADOW PROD TOGGLE
########

    $sha_env = SHADOW_PROD_ENV(false);
    $a = $GLOBALS[$SITE];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../tpsMACHINE.php';  // THE TPS MACHINE 

$cUID    = SKY_GET_cUID($event_time);
$tUID    = SKY_GET_tUID($event_time);


## GET TAGS & FILL CATALOGS (__-SONG-CATALOG-__)
$GLOBALS['JUKEID'] = getJUKED($_POST['artist']) . '_' . getJUKED($_POST['song_title']) . '.juke';
$w = $GLOBALS[$SITE];
$RAW_TAGS = $_POST['POST__TAGS'] ?? '';
$link = $_POST['link'] ?? '';
$artist = $_POST['artist'] ?? '';
$song = $_POST['song_title'] ?? '';

// ============================================================================
// OKAY LETS MAKE A CHESTER CRATE OF THIS BIT OF STUFFS! 
//=============================================================================

catalogJUKEBOX($a,$RAW_TAGS, $sha_env, $link, $artist, $song, $cUID);
chestersCRATES($sha_env, $a, $cUID, $tpstime, $event_time, $tUID, $timezone);
charliesTHREADS($sha_env, $tpstime);
catalogUNIX($tpstime, $cUID, $sha_env);

//=============================================================================
// OH $@%! -- DON'T FORGET YOUR TPS REPORT
// ============================================================================

$tpsDATA = buildTPS($tpstime, $ms, $tzone, $event_time);

$router_1 = ROUTE('d', $sha_env);
$TRACKER_KEEPER = $router_1 . '_timeKEEPER/tps_reports/' . $syear . '/' . substr($tpstime, 0, 6) . '-block/';
    aleph($TRACKER_KEEPER);
  
  $tpsReport = $TRACKER_KEEPER . '/' . substr($tpstime, 0, 6) . '-.tps.json';
  $tpsjson = file_get_contents($tpsReport);
  $tpss = json_decode($tpsjson, true);

  $tpss = fileTPS($tUID, $cUID, $tzone, $event_time, $tpsDATA, $tpss);
  file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));

}

