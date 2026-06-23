<?php 
$GLOBALS['TOOL'] = [
    "SHADOWENVO" => false,
    "NAME" => "cuBOOK",
    "FUNCTION" => "GuestPOST",
    "CATALOG_SLUG" => "cuBOOK Guest Entry",
    "TYPE" => "guestcu",
    "VERSION" => 3
    ];

global $SIGFIG;
$SIGFIG['cuBOOK'] = [
    "GuestPOST" => [
        "skyline-standard" => [
            "USER" => "NAME:",
            "UserHint" => "",
            "MESSAGE" => "YOUR GREETING:",
            "MsgHint" => "",
            "Confirmation_Msg" => "ICU! SUCCESSFULLY Transmitted!",
            "Submit_Button" => "submit guestPOST!" 
        ],
        "early-web" => [
            "USER" => "USERNAME:",
            "UserHint" => "whatever name you want!",
            "MESSAGE" => "YOUR GREETING:",
            "MsgHint" => "",
            "Confirmation_Msg" => "ICU! SUCCESSFULLY Transmitted!",
            "Submit_Button" => "submit guestPOST!" 
        ],
        "classic" => [
            "USER" => "USERNAME:",
            "UserHint" => "whatever name you want!",
            "MESSAGE" => "YOUR GREETING:",
            "MsgHint" => "",
            "Confirmation_Msg" => "ICU! SUCCESSFULLY Transmitted!",
            "Submit_Button" => "submit guestPOST!" 
        ],
    ]
];