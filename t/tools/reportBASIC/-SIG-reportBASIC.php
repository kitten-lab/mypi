

<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
--                    SIG FILE FOR TOOLS                      --
----------------------------------------------------------------*/

$GLOBALS['TOOL'] = [
    "SHADOWENVO" => false,
    "NAME" => "reportBASIC",
    "FUNCTION" => "IntakeReport",
    "ACTOR" => $_POST['POST__REPORTER'] ?? null,
    "CATALOG_SLUG" => "reportBASIC report",
    "TYPE" => "report"    
];


global $SIGFIG;
$SIGFIG['reportBASIC'] = [
    "IntakeReport" => [
        "skyline-standard" => [
            "user"              => $GLOBALS['MATERIAL']['USER'],
            "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'],
            "Reporter"          => "Report Maker",
            "Reporter_plhldr"   => "Name Yourself",
            "Reporter_default"  => "",
            "Topic"             => "Report Topic",
            "Topic_plhldr"      => "The Reason for your Report",
            "Text"              => "Report Contents",
            "Text_plhldr"       => "Enter your report here.",
            "UNIX"              => "FOR INTERNAL USE ONLY",
            "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
            "Confirmation_Msg"  => "You have been witnessed.",
            "Submit_Button"     => "Submit Report",
        ],
        "omansOmens" => SIGFIG_omansOmens(),
        "tee-hee-secrets" => [
                "user"              => $GLOBALS['MATERIAL']['USER'],
                "assistant"         => $GLOBALS['MATERIAL']['ASSISTANT'],
                "Reporter"          => "<span class='teehee'>The Lil' Secret Keeper</span>",
                "Reporter_plhldr"   => "Name Yourself",
                "Reporter_default"  => "ANON-XXX",
                "Topic"             => "What Did Ya Know?",
                "Topic_plhldr"      => "The Reason for your Report",
                "Text"              => "Tell me ALL the deets!",
                "Text_plhldr"       => "Enter your report here.",
                "UNIX"              => "FOR INTERNAL USE ONLY",
                "UNIX_plhldr"       => "KNOWN U-StampS ONLY",
                "Confirmation_Msg"  => "TEE HEE!",
                "Submit_Button"     => "WHISPER TO THE CU",
                "Reset_Button"     => "CLEAR THE AIR (reset)",
        ],
    ],
    "ViewList" => [
        "skyline-standard" => [],
        "omansOmens" => [],
        "tee-hee-secrets" => []
        ],

];

?>