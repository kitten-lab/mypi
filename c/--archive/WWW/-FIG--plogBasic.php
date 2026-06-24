<?php /* 

==================== C O N F I G . f i l e  ==================== 
================================================================
----------------------------------------------------------------
~               logger.basic  standard config file             ~
----------------------------------------------------------------
Adjust to your preferred system language. If none are provided,
the system defaults will be appended. */
$GLOBALS['plogBasic_GaiaTime'] = true;
$GLOBALS['plogBasicAdd'] = [

    "Leaf_Topic_placeholder" =>  /** 
    the display text inside the subject
    DISPLAY: **/ "$sys TOPIC REPORT",

    "Leaf_Text_placeholder" =>  /** 
    the display text inside the body box
    DISPLAY: **/ "BODY OF THE REPORT",

    "Confirmation_Msg" =>  /** 
    the confirmation message after POST
    DISPLAY: **/ "Your REPORT is OBSERVED.",

    "Submit_Button" =>  /** 
    the confirmation message after POST
    DISPLAY: **/ "REPORT",
    
]; 

$GLOBALS['plogBasicList'] = [

    "Page_Link" => /**
    DISPLAY: **/ "view",

    "Page_Key" => /**
    DISPLAY: **/ "personal-log"
]

?>