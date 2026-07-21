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
            "USER_pl" => "whatever name you want!",
            "UserHint" => "whatever name you want!",
            "MESSAGE" => "YOUR GREETING:",
            "MESSAGE_pl" => "leave a note!",
            "MsgHint" => "leave a note!",
            "Confirmation_Msg" => "ICU! SUCCESSFULLY Transmitted!",
            "Submit_Button" => "submit guestPOST!" 
        ],
        "classic" => [
            "USER" => "USERNAME:",
            "USER_pl" => "whatever name you want!",
            "UserHint" => "whatever name you want!",
            "MESSAGE" => "YOUR GREETING:",
            "MESSAGE_pl" => "leave a note!",
            "MsgHint" => "leave a note!",
            "Confirmation_Msg" => "ICU! SUCCESSFULLY Transmitted!",
            "Submit_Button" => "submit guestPOST!" 
        ],
    ]
];