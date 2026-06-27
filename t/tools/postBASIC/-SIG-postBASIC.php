<?php 
define("SHADOW_TOGGLE", false);


$GLOBALS['TOOL'] = [
    "SHADOWENVO" => SHADOW_TOGGLE,
    "NAME" => "postBASIC",
    "FUNCTION" => "Poster",
    "CATALOG_SLUG" => "postBASIC post",
    "TYPE" => "post",
    "VERSION" => 4,
    ];


global $SIGFIG;
$SIGFIG['postBASIC'] = [
    "MakePost" => [
        "skyline-standard" => [
                "user"              => "user",
                "assistant"         => "assistant",
                "post_topic"     => "Section Heading",
                "post_topic_pl"  => "test",
                "post_leaf"        => "Fragment Content",
                "post_leaf_pl"     => "the content of your fragment",
                "POST__TAGS"        => "Charlie Threads",
                "POST__TAGS_plhldr" => "TAG FORMAT: this*connects>that,this",
                "POST__EVENT_UNIX"  => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Fragment",
        ],
        "early-web" => [
                "user"              => "user",
                "assistant"         => "assistant",
                "post_topic"     => "Section Heading",
                "post_topic_pl"  => "test",
                "post_leaf"        => "Fragment Content",
                "post_leaf_pl"     => "the content of your fragment",
                "POST__TAGS"        => "Charlie Threads",
                "POST__TAGS_plhldr" => "TAG FORMAT: this*connects>that,this",
                "POST__EVENT_UNIX"  => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Fragment",
        ],
        "classic" => [
                "user"              => $GLOBALS['MATERIAL']['USER'] ?? 'user',
                "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'] ?? 'assistant',
                "post_topic"     => "Title",
                "post_topic_pl"  => "",
                "post_leaf"        => "Body",
                "post_leaf_pl"     => "the content",
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