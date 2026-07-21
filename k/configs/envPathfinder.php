<?php
require_once 'auth_check.php';
// Local machine tag: COMMANDCENTER9 (this box). ROSEWOOD8 = old laptop; still treated as local if set.
// Online roots exist for later; daily use is local.
global $ENV;
$ENV = 'COMMANDCENTER9';
date_default_timezone_set("America/New_York");

if (!function_exists('mypi_env_is_local')) {
    function mypi_env_is_local($env = null) {
        global $ENV;
        $e = $env ?? $ENV ?? '';
        return in_array($e, ['COMMANDCENTER9', 'ROSEWOOD8', 'LOCAL'], true);
    }
}

// Letter hosts vs public imported.to
if (mypi_env_is_local($ENV)) {
    define('a_root', 'http://a');
    define('b_root', 'http://b');
    define('d_root', 'http://d');
    define('k_root', 'http://k');
    define('i_root', 'http://i');
    if (!defined('mypi_LOCAL')) {
        define('mypi_LOCAL', true);
    }
} else {
    define('a_root', 'https://a.imported.to');
    define('d_root', 'https://d.imported.to');
    define('b_root', 'https://b.imported.to');
    define('k_root', 'https://k.imported.to');
    define('i_root', 'https://i.imported.to');
    if (!defined('mypi_LOCAL')) {
        define('mypi_LOCAL', false);
    }
}



$GLOBALS['MATERIAL']['TYPE'] = "Obsidian Vault";
$GLOBALS['MATERIAL']['SOURCE']['NAME'] = "Terminal IO";
$GLOBALS['MATERIAL']['SOURCE']['ID'] = "_Chesters Imports/Terminal IO";
$GLOBALS['MATERIAL']['SOURCE']['CREATED'] = 1757980800;
$GLOBALS['MATERIAL']['SOURCE']['LAST_MODIFIED'] = 1734134400;
$GLOBALS['MATERIAL']['REFS'] = ["mod:sdk-808", "green", "terminal", "vault-material"];
$GLOBALS['MATERIAL']['DETAILS'] = "This SAM inherited the IMPORT SHOP, but can't remember Chester.";

?>