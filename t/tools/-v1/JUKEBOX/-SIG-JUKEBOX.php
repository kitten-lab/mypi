<?php 
$GLOBALS['TOOL'] = [
    "NAME" => "JUKEBOX",
    "FUNCTION" => "UploadSong",
    "ACTOR" => $_POST['UPLOADER'],
    "OBJECT" => $GLOBALS['JUKEID'],
    "CATALOG_SLUG" => "JUKEBOX upload",
    "TYPE" => "jukebox",
    "VERSION" => 1,
    "SIGFIG" => [
        "skyline-standard" => [
            "UploadSong" => [
                "Topic"             => "Report Topic",
                "Topic_plhldr"      => "The Reason for your Report",
                "Text"              => "Report Contents",
                "Text_plhldr"       => "Enter your report here.",
                "UNIX"              => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "You have been witnessed.",
                "Submit_Button"     => "Submit Report",
                
            ]
        ]
]];