<?php 
global $MATERIAL;
global $SITE;

$GLOBALS['TOOL'] = [
    "SHADOWENVO" => false,
    "NAME" => "postBASIC",
    "FUNCTION" => "MakePost",
    "ACTOR" => $GLOBALS[$SITE]['MOD_SLUG'],
    "CATALOG_SLUG" => "postBASIC post",
    "TYPE" => "post",
    "VERSION" => 3,
    "SIGFIG" => [
        "skyline-standard" => [
            "MakePost" => [
                "user"              => $MATERIAL['USER'],
                "assistant"         => $MATERIAL['ASSISTANT'],
                "Topic"             => "Post Topic",
                "Topic_plhldr"      => "",
                "Content"           => "Post Content",
                "Content_plhldr"    => "",
                "Tags"              => "Tag Content",
                "Tags_plhldr"       => "",
                "UNIX"              => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Post",
                "link"     => "post-view/",
                
            ],
            "postBasic" => [],
            "ViewList" => [],

        ]
]];


global $SIGFIG;
$SIGFIG['postBASIC'] = [
    "MakePost" => [
        "skyline-standard" => [
                "user"              => $MATERIAL['USER'],
                "assistant"         => $MATERIAL['ASSISTANT'],
                "Topic"             => "Post Topic",
                "Topic_plhldr"      => "",
                "Content"           => "Post Content",
                "Content_plhldr"    => "",
                "Tags"              => "Tag Content",
                "Tags_plhldr"       => "",
                "UNIX"              => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Post",
                "link"     => "post-view/",
        ],
        "classic" => [
                "user"              => $MATERIAL['USER'],
                "assistant"         => $MATERIAL['ASSISTANT'],
                "Topic"             => "Post Topic",
                "Topic_plhldr"      => "",
                "Content"           => "Post Content",
                "Content_plhldr"    => "",
                "Tags"              => "Tag Content",
                "Tags_plhldr"       => "",
                "UNIX"              => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "POSTED SUCCESSFULLY!",
                "Submit_Button"     => "Store Post",
                "link"     => "post-view/",]
    ],
    "postBasic" => [
        "skyline-standard" => [],
        "classic" => []
    ],
    "ViewList" => [
        "skyline-standard" => [],
        "classic" => []
    ],
];