<?php
require_once __DIR__ . '/../../systems/rehydrateSelf.php';
require_once __DIR__ . '/../skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $POST__MAIL_TOPIC =  $_POST['POST__MAIL_TOPIC']; 
    $POST__MAIL_TEXT =   $_POST['POST__MAIL_TEXT']; 
    $POST__SYS =         $_POST['POST__SYS'];
    $POST__DOM =         $_POST['POST__DOM'];
    $POST__MOD =         $_POST['POST__MOD'];
    $TO__SYS =           $_POST['TO__SYS'];
    $TO__DOM =           $_POST['TO__DOM'];
    $TO__MOD =           $_POST['TO__MOD'];
    $POST__PV =          $_GET['pv'] ?? '__UNDISCLOSED__';
    $POST__TIMEZONE =    $_POST['POST__TZ'];

## TOOL SIG FILE
    $TOOL_FUNC = "SEND A MAIL";
    $TOOL_LOC = "mailroomBasic";
    $TOOL_NAME = "actorSendMail";
        ## SET YOUR KDE FOR THIS TOOL ##
        $KDE__ERROR_TYPE = $TOOL_FUNC;
        $KDE__SOURCE = $TOOL_NAME;
        $KDE__ECHO_CHAIN = $POST__SYS . ' > ' . $POST__DOM . ' > ' . $POST__MOD;
        $KDE__ERROR = "THE SKY LOCATED A HOME IN SIGHT. CONSIDER LOCATING THAT HOME OR USE A UNIQUE WORLD_NAME.";
        ################################

    $cUID = 'cUID-' . strtoupper(bin2hex(random_bytes(8)));
        $CHEST__HEADER = $POST__MOD . " SENT EMAIL TO " . $TO__MOD;
        $CHEST__CONTEXT = $POST__MAIL_TOPIC;
        $CHEST__ACTOR = $TOOL_NAME;
        $CHEST__EVENT = $TOOL_FUNC;
        $CHEST__EVENT_LOCATON = $TOOL_LOC;


    $tUID = 'tUID-' . date('Ymd') . '.' . strtoupper(bin2hex(random_bytes(3)));
        $unix = time();
        $tzone = $POST__TIMEZONE;
        $ms = round(microtime(true) * 1000);
        $time = new DateTime("@$unix");
        $time->setTimezone(new DateTimeZone($tzone));
        $timezone = $time->format("e");
        $localtime = $time->format("h:i:sA");
        $simpledate = date('Y-m-d');

        function buildTPS($unix, $ms,$tzone) {
            $tpsDT = new DateTime("@$unix");
            $tpsDT->setTimezone(new DateTimeZone("UTC"));
            $year = (int)$tpsDT->format('x');

            return [
                "UNIX" => $unix,
                "POST__TZONE" => $tzone,
                "TPS__TZONE" => "UTC",
                "TPS__netLoop" => (int)$tpsDT->format('B'),
                "TPS__millennium" => intdiv($year, 1000),
                "TPS__century" => intdiv($year, 100),
                "TPS__decade" => intdiv($year, 10),
                "TPS__year" => $year,
                "TPS__leap" => (int)$tpsDT->format('L'),
                "TPS__month" => (int)$tpsDT->format("n"),
                "TPS__week" => (int)$tpsDT->format("W"),
                "TPS__dayOfYear" => (int)$tpsDT->format("z"),
                "TPS__dayOfMonth" => (int)$tpsDT->format("j"),
                "TPS__dayOfWeek" => (int)$tpsDT->format("w"),
                "TPS__hour" => (int)$tpsDT->format("G"),
                "TPS__minute" => (int)$tpsDT->format("i"),
                "TPS__second" => (int)$tpsDT->format("s"),
                "TPS__ms" => $ms % 1000,
            ];
            }

    $tpsDATA = buildTPS($unix,$ms,$tzone);
    
$ROUTE__LINE = ROUTE("d");

  
   $ROUTE = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . $TOOL_LOC . '/' . $POST__SYS;

if (!is_dir($ROUTE)) {
    mkdir($ROUTE, 0775, true);
}
  $CHEST = $ROUTE . '/data.json';

  // Read existing data
  $json = file_get_contents($CHEST);
  $CHEST_CONTENTS = json_decode($json, true);

  if (!$CHEST_CONTENTS) {
    $CHEST_CONTENTS = [];
  }

  // Create new post
  $CHEST_CONTENTS[$cUID] = [
    "TUID__REF" => $tUID, 
    // CUSTOM CHEST DETAILS HERE

    "POST__MAIL_TOPIC" => $POST__MAIL_TOPIC,
    "POST__MAIL_TEXT" => $POST__MAIL_TEXT, 
    "POST__SYS" => $POST__SYS,
    "POST__DOM" => $POST__DOM,
    "POST__MOD" => $POST__MOD,
    "TO__SYS" => $TO__SYS,
    "TO__DOM" => $TO__DOM,
    "TO__MOD" => $TO__MOD,

    //DO NOT MODIFY BELOW
    "META_DATA" => [
        "UNIX" => $unix,
        "GAIA__DATE" => $simpledate,
        "GAIA__TIME" => $localtime,
        "GAIA__TZONE" => $timezone,
        "POST__VIEWPORT" => $POST__PV,
        "TOOL__LOCATION" => $TOOL_LOC,
        "TOOL__NAME" => $TOOL_NAME,
        "TOOL__FUNCTION" => $TOOL_FUNC,
        "CHEST__VERSION" => 2,
    ]
  ];

  file_put_contents($CHEST, json_encode($CHEST_CONTENTS, JSON_PRETTY_PRINT));


// ============================================================================
// NOW LETS MAKE A ECHO-BALLBACK SO WE CAN SEE WHAT WE BEEN UP TO!
// ============================================================================

$ROUTE__LINE = ROUTE('z');

$dir = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . 'ECHO/' . date('Y') . '-' . date('m') . '/';
    if (!is_dir($dir)) { mkdir($dir, 0775, true); }   

  $file = $dir . '/' . $simpledate . '_dailyechos.json';
  $json = file_get_contents($file);
  $echos = json_decode($json, true);

  if (!$echos) {
    $echos = [];
  }

  $echos[$localtime . ': ' . $unix] = [
    "CUID__REF" => $cUID, 
    "TUID__REF" => $tUID,
    "ECHO__CHAIN" => $KDE__ECHO_CHAIN,
    "CHEST__HEADER" => $CHEST__HEADER,
    "CHEST__CONTEXT" => $CHEST__CONTEXT,
    "META__DATA" => [
        "EVENT__ACTION" => $TOOL_FUNC,
        "EVENT__ACTOR" => $TOOL_LOC,
        "EVENT__TOOL" => $TOOL_NAME,
        "POST__PV" => $POST__PV,
        "GAIA__DATE" => $simpledate,
        "GAIA__TIME" => $localtime,
        "GAIA__TZONE" => $timezone,
        "ECHO__VERSION" => 2,
    ]
  ];

  file_put_contents($file, json_encode($echos, JSON_PRETTY_PRINT));


// YAAAAAAY DONE AGAIN!
// ============================================================================

// ============================================================================
// OH $@%! -- DON'T FORGET YOUR TPS REPORT
// ============================================================================
  $recordKeeper = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . 'TPS';
    if (!is_dir($recordKeeper)) { mkdir($recordKeeper, 0775, true); }
  
  $tpsReport = $recordKeeper . '/tpsReport_' . $simpledate . '_data.json';
  $json = file_get_contents($tpsReport);
  $tpss = json_decode($json, true);

    if (!$tpss) {
        $tpss = [];
    }

    if (isset($tpss[$tUID])) {
        die("Already exists in this Location.");
    }

    $tpss[$tUID] = [
        "CUID__REF" => $cUID,
        "POST__SYS" => $POST__SYS,
        "POST__DOM" => $POST__DOM,
        "POST__MOD" => $POST__MOD,
        "POST__VIEWPORT" => $POST__PV,
        "TPS__VERSION" => 2,
        "TPS__REPORT" => $tpsDATA,
    ];

  file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));


}