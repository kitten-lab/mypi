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

    "Title" =>/** 
    the Section Title
    DISPLAY: **/ "Skyline Public Recorder",

    "Content" =>  /** 
    the section text displayed below the header
    DISPLAY: **/ "The SKYLINE May have eyes in every cell, but 
    you have your own eyes too. Do you have something to report? 
    Consider entering it below.",

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

    "Title" =>  /** 
    the section title for the listing of posts
    DISPLAY: **/ "Contribution Listings",
    
    "Content" =>  /** 
    the section text displayed on the listing page
    DISPLAY: **/ "Viewing all listings from $mod in $sys.$dom.",

    "Page_Link" => /**
    DISPLAY: **/ "view-report"
]

?>