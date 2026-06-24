<?php
require_once __DIR__ . '/../../systems/rehydrateSelf.php';
require_once __DIR__ . '/../skyGenesis/functions.php'; //GET SHADOW PROD TOGGLE
$SHADOW_PROD_TOGGLE = SHADOW_PROD_ENV(false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $POST__SYS =         $_POST['POST__SYS'];
    $POST__DOM =         $_POST['POST__DOM'];
    $POST__MOD =         $_POST['POST__MOD'];
    $POST__PV =          $_GET['pv'] ?? '__UNDISCLOSED__';
    $POST__TIMEZONE =    $_POST['POST__TZ'];

    $GEN__KEY_OPENSKY =      $_POST['GEN__KEY_OPENSKY'];
    $GEN__KEY_FOR_HOUSE =    $_POST['GEN__KEY_FOR_HOUSE'];
    $GEN__KEY_FOR_ROOM =     $_POST['GEN__KEY_FOR_ROOM'];
    $GEN__KEY_NAME =         $_POST['GEN__KEY_NAME'];
    $GEN__KEY_SKYBODY =      $_POST['GEN__KEY_SKYBODY'];

    ## TOOL SIG FILE
    $TOOL_FUNC = "GENERATE A KEY";
    $TOOL_LOC = "keyMaker";
    $TOOL_NAME = "actorMakeKey";
        ## SET YOUR KDE FOR THIS TOOL ##
        $KDE__ERROR_TYPE = $TOOL_FUNC . " DUPLICATE REJECTED";
        $KDE__SOURCE = $TOOL_NAME;
        $KDE__ECHO_CHAIN = $POST__SYS . ' > ' . $POST__DOM . ' > ' . $POST__MOD;
        $KDE__ERROR = "THE SKY LOCATED A HOME IN SIGHT. CONSIDER LOCATING THAT HOME OR USE A UNIQUE WORLD_NAME.";
        ################################

    $cUID = 'cUID-' . strtoupper(bin2hex(random_bytes(8)));
        $CHEST__HEADER = "GENERATED " . $GEN__KEY_NAME;
        $CHEST__CONTEXT = 'CREATED BY ' . $POST__SYS;
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
    

$ROUTE__LINE = ROUTE("m");

    $GEN__KEY_LOCATION = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . "rooms/" . $GEN__KEY_FOR_HOUSE . "/" . $GEN__KEY_FOR_ROOM . "/";
    if (!is_dir($GEN__KEY_LOCATION)) { mkdir($GEN__KEY_LOCATION, 0775, true); }  
     
    $GEN__KEY = $GEN__KEY_LOCATION . $GEN__KEY_NAME . ".php"; // Unique filename  
$KEY = <<<KEY_CONTENTS
<?php 
openSky("{$GEN__KEY_OPENSKY}");
$GEN__KEY_SKYBODY;
closeSky();
KEY_CONTENTS;

file_put_contents($GEN__KEY, $KEY);

// TIME TO MAKE A CRATE
$ROUTE__LINE = ROUTE('d');

$ROUTE = $GLOBALS['sonar'] . $SHADOW_PROD_TOGGLE . $ROUTE__LINE . $TOOL_LOC . '/';
    if (!is_dir($ROUTE)) { mkdir($ROUTE, 0775, true); }   

  $CHEST = $ROUTE . 'data.json';

  $json = file_get_contents($CHEST);
  $CHEST_THINGS = json_decode($json, true);

  if (!$CHEST_THINGS) {
    $CHEST_THINGS = [];
  }

  $CHEST_THINGS[$cUID] = [
    "TUID__REF" => $tUID, 
    // CUSTOM CHEST DETAILS HERE

    "GEN__KEY_OPENSKY" => $GEN__KEY_OPENSKY,
    "GEN__KEY_FOR_HOUSE" => $GEN__KEY_FOR_HOUSE,
    "GEN__KEY_FOR_ROOM" => $GEN__KEY_FOR_ROOM,
    "GEN__KEY_NAME" => $GEN__KEY_NAME,
    "GEN__KEY_SKYBODY" => $GEN__KEY_SKYBODY,

    //DO NOT MODIFY BELOW
    "META_DATA" => [
        "UNIX" => $unix,
        "GAIA__DATE" => $simpledate,
        "GAIA__TIME" => $localtime,
        "GAIA__TZONE" => $timezone,
        "POST__SYS" => $POST__SYS,
        "POST__DOM" => $POST__DOM,
        "POST__MOD" => $POST__MOD,
        "POST__VIEWPORT" => $POST__PV,
        "TOOL__LOCATION" => $TOOL_LOC,
        "TOOL__NAME" => $TOOL_NAME,
        "TOOL__FUNCTION" => $TOOL_FUNC,
        "CHEST__VERSION" => 2,
    ]
  ];

  file_put_contents($CHEST, json_encode($CHEST_THINGS, JSON_PRETTY_PRINT));
// ============================================================================
// YAY DONE!

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
