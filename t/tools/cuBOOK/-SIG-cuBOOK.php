<?php
define('SHADOW_TOGGLE', false);

$GLOBALS['TOOL'] = [
    'SHADOWENVO' => SHADOW_TOGGLE,
    'NAME' => 'cuBOOK',
    'FUNCTION' => 'GuestPOST',
    'CATALOG_SLUG' => 'cuBOOK Guest Entry',
    'TYPE' => 'guestcu',
    'VERSION' => 6,
];

global $SIGFIG;
$SIGFIG['cuBOOK'] = [
    'GuestPOST' => [
        'skyline-standard' => [
            'USER' => 'NAME:',
            'USER_pl' => 'your name',
            'MESSAGE' => 'YOUR GREETING:',
            'MESSAGE_pl' => 'leave a note!',
            'Confirmation_Msg' => 'ICU! Stored in ledger.',
            'Submit_Button' => 'submit guestPOST!',
        ],
        'early-web' => [
            'USER' => 'USERNAME:',
            'USER_pl' => 'whatever name you want!',
            'MESSAGE' => 'YOUR GREETING:',
            'MESSAGE_pl' => 'leave a note!',
            'Confirmation_Msg' => 'ICU! Stored in ledger.',
            'Submit_Button' => 'submit guestPOST!',
        ],
        'classic' => [
            'USER' => 'USERNAME:',
            'USER_pl' => 'whatever name you want!',
            'MESSAGE' => 'YOUR GREETING:',
            'MESSAGE_pl' => 'leave a note!',
            'Confirmation_Msg' => 'ICU! Stored in ledger.',
            'Submit_Button' => 'submit guestPOST!',
        ],
    ],
    'ViewCUs' => [
        'skyline-standard' => [],
        'early-web' => [],
        'classic' => [],
    ],
];
