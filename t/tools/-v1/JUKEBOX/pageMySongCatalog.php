<?php $a = $GLOBALS['SITE'];

require_once $GLOBALS['INTERA']['TOOLS'] . 'skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
require_once __DIR__ . '/-SIG-JUKEBOX.php'; //GET SHADOW PROD TOGGLE

$FIG = getFIG("JUKEBOX", "UploadSong"); 



$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(true);
$ROUTE__LINE = ROUTE('d', $SHADOW_PROD_TOGGLE);

$ROUTE = $ROUTE__LINE . '_charlieCATALOG/song_catalog/';
$CATALOG = $ROUTE . $a['SYS_SLUG'] . '-' . $a['DOM_SLUG'] . '.songs.catalog.json';
$LISTINGS = json_decode(file_get_contents($CATALOG), true);


foreach ($LISTINGS as $LISTING => $ARTIST) {
foreach ($ARTIST as $ARTIST => $SONG) {
    echo "<br><br>" . $LISTING . ' - ' . $SONG['song_title'] . "<br>";
foreach ($SONG['tagged_as'] as $tag_type => $tags) {
    echo '' . $tag_type . ': ';
foreach ($tags as $group => $tag) {
foreach ($tag as $v =>$t) {
    echo '#' . $group . '/' . $t . ' ';
}}
}
}}
?>
