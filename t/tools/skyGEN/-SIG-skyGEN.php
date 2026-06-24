<?php 
define("SHADOW_TOGGLE", false);

$GLOBALS['TOOL'] = [
    "SHADOWENVO" => SHADOW_TOGGLE,
    "NAME" => "skyGEN",
    "FUNCTION" => "Generate Surface",
    "CATALOG_SLUG" => "Surface Launch",
    "TYPE" => "generator",
    "VERSION" => 2
    ];


global $SIGFIG;
$SIGFIG['skyGEN'] = [
    "SkyLaunch" => [
        'classic' => [
          "gen-WORLD_SLUG" => "Surface Slug Name (no spaces)",
          "gen-WORLD_DISPLAY" => "Surface Display Name",
          "gen-DOM_SLUG" => "First Room Slug (no spaces)",
          "gen-DOM_DISPLAY" => "Room Display Name",
          "gen-MOD_SLUG" => "First MOD Slug For Room (no spaces)",
          "gen-MOD_DISPLAY" => "Display name for MOD",
          "gen-KEY_SLUG" => "Key Slug (no spaces)",
          "gen-KEY_DISPLAY" => "Page Title (no spaces)",
          "gen-URI" => "Block URI (same as World if root)",
          "gen-LOVERS_MARK" => "Leave the mark of the lover",
        ]
    ]
];