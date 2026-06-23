<?php
require __DIR__ . '/../../systems/rehydrateSelf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CHANGE PER TOOL //
  $venID = $_POST['VEN1'] . '-' . $_POST['VEN2'];
  $chestNOTE = $venID; 
  $chestCONTENT = $_POST['keyType']; 
  $betACTION = "REGISTERED: VEN";
  $reportHEAD = "venRegistrar|actorAdd";


    // DO NOT TOUCHY //
  $tpsUID = 'tUID-' . date('Ymd') . '.' . strtoupper(bin2hex(random_bytes(3)));
  $chestUID = 'cUID-' . strtoupper(bin2hex(random_bytes(8)));
  $idCHAIN = $_POST['betSys'] . '|' . $_POST['betDom'] . '|' . $_POST['betMod'];


  $unix = time();
  $tzone = $_POST['betTZone'];
  $ms = round(microtime(true) * 1000);
  $time = new DateTime("@$unix");
  $time->setTimezone(new DateTimeZone($tzone));

  $betSYS = $_POST['betSys'];
  $betDOM = $_POST['betDom'];
  $betMOD = $_POST['betMod'];
  $timezone = $time->format("e");
  $localtime = $time->format("h:i:sA");

function buildTPS($unix, $ms,$tzone) {
  $tpsDT = new DateTime("@$unix");
  $tpsDT->setTimezone(new DateTimeZone("UTC"));
  $year = (int)$tpsDT->format('x');

  return [
    "chest.UNIX" => $unix,
    "event.TZONE" => $tzone,
    "tps.TZONE" => "UTC",
    "tps.netLoop" => (int)$tpsDT->format('B'),
    "tps.millennium" => intdiv($year, 1000),
    "tps.century" => intdiv($year, 100),
    "tps.decade" => intdiv($year, 10),
    "tps.year" => $year,
    "tps.leap" => (int)$tpsDT->format('L'),
    "tps.month" => (int)$tpsDT->format("n"),
    "tps.week" => (int)$tpsDT->format("W"),
    "tps.dayOfYear" => (int)$tpsDT->format("z"),
    "tps.dayOfMonth" => (int)$tpsDT->format("j"),
    "tps.dayOfWeek" => (int)$tpsDT->format("w"),
    "tps.hour" => (int)$tpsDT->format("G"),
    "tps.minute" => (int)$tpsDT->format("i"),
    "tps.second" => (int)$tpsDT->format("s"),
    "tps.ms" => $ms % 1000,
  ];
}

$tpsDATA = buildTPS($unix,$ms,$tzone);

// ============================================================================
// OKAY LETS MAKE A CHESTER CRATE OF THIS BIT OF STUFFS! 
// ============================================================================

  $dir =  $GLOBALS['sonar'] . 'z/venRegistrar';

    if (!is_dir($dir)) { mkdir($dir, 0775, true); }   

  $file = $dir . '/registry.json';

  // Read existing data
  $json = file_get_contents($file);
  $vens = json_decode($json, true);
  

  if (!$vens) {
    $vens = [];
  }


    if (!isset($vens[$venID])) {
        $vens[$venID] = [];
    } else { die("No!"); }

  // Create new post
  $vens[$venID] = [
        "ven.keyTYPE" => $_POST['keyType'],
        "ven.keyLABEL" => $_POST['keyLabel'],
        "ven.keyNOTES" => $_POST['registryNote'],
        "ven.scrubLABEL" => $_POST['scrubName'],
        "meta.DATA" => [
            "chest.UID" => $chestUID,
            "tps.REF" => $tpsUID, 
            "acting.SYSTEM" => $_POST['betSys'],
            "acting.CTRLS" => $_POST['betDom'],
            "acting.DOLLY" => $_POST['betMod'],
            "acting.VIEWPORT" => $_GET['pv'] ?? '__UNDISCLOSED__',

                ],
  ];


  // Save it
  file_put_contents($file, json_encode($vens, JSON_PRETTY_PRINT));

// ============================================================================
// YAY DONE!

// ============================================================================
// NOW LETS MAKE A ECHO-BALLBACK SO WE CAN SEE WHAT WE BEEN UP TO!
// ============================================================================
  $dir =  $GLOBALS['sonar'] . 'z/echoTraceback/' . date('Y') . '-' . date('m') . ' /';
    if (!is_dir($dir)) { mkdir($dir, 0775, true); }   

  $file = $dir . date('Y-m-d') . '_dailyechos.json';
  $json = file_get_contents($file);
  $echos = json_decode($json, true);

  if (!$echos) {
    $echos = [];
  }

  $echos[$localtime . ': ' . $chestNOTE] = [
    "chest.REF" => $chestUID, 
    "tps.REF" => $tpsUID, 
    "chest.NOTE" => $chestCONTENT,
    "gaia.DATE" => date('Y-m-d'),
    "gaia.TIME" => $localtime,
    "gaia.TZONE" => $timezone,
    "meta.DATA" => [
    "echo.idCHAIN" => $idCHAIN,
    "acting.VIEWPORT" => $_GET['pv'] ?? '__UNDISCLOSED__',
    "bet.ACTION" => $betACTION,
    "bet.REPORTER" => $reportHEAD,
    "echo.VERSION" => 1,
    ]
  ];

  file_put_contents($file, json_encode($echos, JSON_PRETTY_PRINT));


// YAAAAAAY DONE AGAIN!
// ============================================================================

// ============================================================================
// OH $@%! -- DON'T FORGET YOUR TPS REPORT
// ============================================================================
  $recordKeeper = $GLOBALS['sonar'] . 'z/trackerKeeper';
    if (!is_dir($recordKeeper)) { mkdir($recordKeeper, 0775, true); }
  
  $tpsReport = $recordKeeper . '/tpsReport_' . date('Y-m-d') . '_data.json';
  $json = file_get_contents($tpsReport);
  $tpss = json_decode($json, true);

    if (!$tpss) {
        $tpss = [];
    }

    if (isset($tpss[$tpsUID])) {
        die("Already exists in this Location.");
    }

    $tpss[$tpsUID] = [
        "chest.UID" => $chestUID,
        "acting.SYSTEM" => $_POST['betSys'],
        "acting.CTRLS" => $_POST['betDom'],
        "acting.DOLLY" => $_POST['betMod'],
        "acting.VIEWPORT" => $_GET['pv'] ?? '__UNDISCLOSED__',
        "tps.VERSION" => 1,
        "tps.REPORT" => $tpsDATA,
    ];

  file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));


}