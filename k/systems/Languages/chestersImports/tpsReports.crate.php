<?php 
//==============================================================================================
// FUNCTIONS FOR THE TPS MACHINE
//==============================================================================================
function tpsREPORTS(){
    
  // MINI TIME MACHINE
  $tpsDT = TPS_EVENTCALC;
  $tpsDT->setTimezone(new DateTimeZone("UTC"));
  $year = (int)$tpsDT->format('x');

  $TPS_BLOCK = intdiv((int)TPS_TPSTIME, 10000);

  $tpsFiles = ROUTE_TO_SATORA . TPS_SYEAR . '/' . $TPS_BLOCK . '-block/';
    aleph($tpsFiles);
    
  $tpsReport = $tpsFiles . $TPS_BLOCK . '-.tps.json';
  $tpsjson = file_get_contents($tpsReport);
  $tpss = json_decode($tpsjson, true);

  if (!$tpss) {
    $tpss = [];
  }
    
  if (!isset($tpss[tUID])){
    $tpss[tUID] = [
      "tps_version" => 3,
      "cUID" => [cUID],
      "event_slug" => [],
      "import_unix" => [time()],
      "time_certainty" => [],
      "event_timezone" => $_POST['POST__TZ'],
      "tps_timzezone" => "UTC",
      "tps_unix" => TPS_EVENTTIME,
      "tps_report" => [
        "netLoop" => (int)$tpsDT->format('B'),
        "millennium" => intdiv($year, 1000),
        "century" => intdiv($year, 100),
        "decade" => intdiv($year, 10),
        "year" => TPS_SYEAR,
        "leap" => (int)$tpsDT->format('L'),
        "month" => (int)$tpsDT->format("n"),
        "week" => (int)$tpsDT->format("W"),
        "dayOfYear" => (int)$tpsDT->format("z"),
        "dayOfMonth" => (int)$tpsDT->format("j"),
        "dayOfWeek" => (int)$tpsDT->format("w"),
        "hour" => (int)$tpsDT->format("G"),
        "minute" => (int)$tpsDT->format("i"),
        "second" => (int)$tpsDT->format("s"),
        "ms" => TPS_MS % 1000,
      ]
    ];
    
  } else {
    if (!isset($tpss[tUID]['cUID'])){
      $tpss[tUID]['cUID'] = [];
    }
    if (!in_array(cUID, $tpss[tUID]['cUID'])){
      $tpss[tUID]['cUID'][] = cUID;
    }
    if (!isset($tpss[tUID]['import_unix'])){
      $tpss[tUID]['import_unix'] = [];
    }
    if (!in_array(cUID, $tpss[tUID])){
      $tpss[tUID]['import_unix'][] = time();
    }
  }

  file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));
}