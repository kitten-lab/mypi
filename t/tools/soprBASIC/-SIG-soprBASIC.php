<?php 
define("SHADOW_TOGGLE", false);


$GLOBALS['TOOL'] = [
    "SHADOWENVO" => SHADOW_TOGGLE,
    "NAME" => "soprBASIC",
    "FUNCTION" => "AddFragment",
    "CATALOG_SLUG" => "soprBASIC fragment",
    "TYPE" => "sopr",
    "VERSION" => 3,
    ];


global $SIGFIG;
$SIGFIG['soprBASIC'] = [
    "AddFragment" => [
        "skyline-standard" => [
                "user"              => $GLOBALS['MATERIAL']['USER'],
                "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'],
                "soper_section"     => "Section Heading",
                "soper_section_pl"  => "test",
                "soper_leaf"        => "Fragment Content",
                "soper_leaf_pl"     => "the content of your fragment",
                "POST__TAGS"        => "Charlie Threads",
                "POST__TAGS_plhldr" => "TAG FORMAT: this*connects>that,this",
                "POST__EVENT_UNIX"  => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Fragment",
        ],
        "early-web" => [
                "user"              => $GLOBALS['MATERIAL']['USER'],
                "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'],
                "soper_section"     => "Section Heading",
                "soper_section_pl"  => "test",
                "soper_leaf"        => "Fragment Content",
                "soper_leaf_pl"     => "the content of your fragment",
                "POST__TAGS"        => "Charlie Threads",
                "POST__TAGS_plhldr" => "TAG FORMAT: this*connects>that,this",
                "POST__EVENT_UNIX"  => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Fragment",
        ],
        "classic" => [
                "user"              => $GLOBALS['MATERIAL']['USER'],
                "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'],
                "soper_section"     => "Section Heading",
                "soper_section_pl"  => "test",
                "soper_leaf"        => "Fragment Content",
                "soper_leaf_pl"     => "the content of your fragment",
                "POST__TAGS"        => "Charlie Threads",
                "POST__TAGS_plhldr" => "TAG FORMAT: this*connects>that,this",
                "POST__EVENT_UNIX"  => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Fragment",
        ],
    ],
    
    "ViewList" => [
         "skyline-standard" => [],
         "early-web" => [],
         "classic" => []
    ]
];