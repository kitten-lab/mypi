<?php 
require __DIR__ . '/NIM/charlieLOOKUP.php';
require __DIR__ . '/NIM/demoLOOKUP.php';

function aleph($ROUTE){
    // aleph is the ox. it plow the field if there is no directory, so space can exist //
    if (!is_dir($ROUTE)) { mkdir($ROUTE, 0775, true); }
}

function SKY_GET_tUID($event_time){
    $GLOBALS['tUID'] = $event_time  . '.tps';
        $tUID = $GLOBALS['tUID'];
        return $tUID;
}

function SKY_GET_cUID($event_time){
    $GLOBALS['cUID'] = 'crate.' . strtoupper(bin2hex(random_bytes(8)));
        $cUID = $GLOBALS['cUID'];
        return $cUID;

}


//==============================================================================================
// FUNCTIONS FOR THE TPS MACHINE
//==============================================================================================
function tpsREPORTS($sha_env, $tpstime, $ms, $event_time, $syear){
   
    $tpsDT = new DateTime("@$event_time");
    $tpsDT->setTimezone(new DateTimeZone("UTC"));
    $year = (int)$tpsDT->format('x');
    $block = intdiv((int)$tpstime, 10000);


    $router_1 = ROUTE('d', $sha_env);
    $tpsFiles = $router_1 . '_SATORA/tps_reports/' . $syear . '/' . $block . '-block/';
        aleph($tpsFiles);
    
    $tpsReport = $tpsFiles . '/' . $block . '-.tps.json';
    $tpsjson = file_get_contents($tpsReport);
    $tpss = json_decode($tpsjson, true);

    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];

    if (!$tpss) {
        $tpss = [];
    }
    
  if (!isset($tpss[$tUID])){

    $tpss[$tUID] = [
        "tps_version" => 3,
        "cUID" => [$cUID],
        "event_slug" => [],
        "import_unix" => [time()],
        "time_certainty" => [],
        "event_timezone" => $_POST['POST__TZ'],
        "tps_timzezone" => "UTC",
        "tps_unix" => $event_time,
        "tps_report" => [
        "netLoop" => (int)$tpsDT->format('B'),
        "millennium" => intdiv($year, 1000),
        "century" => intdiv($year, 100),
        "decade" => intdiv($year, 10),
        "year" => $year,
        "leap" => (int)$tpsDT->format('L'),
        "month" => (int)$tpsDT->format("n"),
        "week" => (int)$tpsDT->format("W"),
        "dayOfYear" => (int)$tpsDT->format("z"),
        "dayOfMonth" => (int)$tpsDT->format("j"),
        "dayOfWeek" => (int)$tpsDT->format("w"),
        "hour" => (int)$tpsDT->format("G"),
        "minute" => (int)$tpsDT->format("i"),
        "second" => (int)$tpsDT->format("s"),
        "ms" => $ms % 1000,
    ]
    ];
    
    } else {

        if (!isset($tpss[$tUID]['cUID'])){
            $tpss[$tUID]['cUID'] = [];
        }

        if (!in_array($cUID, $tpss[$tUID]['cUID'])){
            $tpss[$tUID]['cUID'][] = $cUID;
        }

        if (!isset($tpss[$tUID]['import_unix'])){
            $tpss[$tUID]['import_unix'] = [];
        }

        if (!in_array($cUID, $tpss[$tUID])){
            $tpss[$tUID]['import_unix'][] = time();
        }
    }



    file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));
    }

function demoTPSReports($sha_env, $tpstime, $ms, $event_time, $syear){
   
    $tpsDT = new DateTime("@$event_time");
    $tpsDT->setTimezone(new DateTimeZone("UTC"));
    $year = (int)$tpsDT->format('x');
    $block = intdiv((int)$tpstime, 10000);


    $router_1 = ROUTE('d', $sha_env);
    $tpsFiles = $router_1 . 'DEMO/_SATORA/tps_reports/' . $syear . '/' . $block . '-block/';
        aleph($tpsFiles);
    
    $tpsReport = $tpsFiles . '/' . $block . '-.tps.json';
    $tpsjson = file_get_contents($tpsReport);
    $tpss = json_decode($tpsjson, true);

    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];

    if (!$tpss) {
        $tpss = [];
    }
    
  if (!isset($tpss[$tUID])){

    $tpss[$tUID] = [
        "tps_version" => 3,
        "cUID" => [$cUID],
        "event_slug" => [],
        "import_unix" => [time()],
        "time_certainty" => [],
        "event_timezone" => $_POST['POST__TZ'],
        "tps_timzezone" => "UTC",
        "tps_unix" => $event_time,
        "tps_report" => [
        "netLoop" => (int)$tpsDT->format('B'),
        "millennium" => intdiv($year, 1000),
        "century" => intdiv($year, 100),
        "decade" => intdiv($year, 10),
        "year" => $year,
        "leap" => (int)$tpsDT->format('L'),
        "month" => (int)$tpsDT->format("n"),
        "week" => (int)$tpsDT->format("W"),
        "dayOfYear" => (int)$tpsDT->format("z"),
        "dayOfMonth" => (int)$tpsDT->format("j"),
        "dayOfWeek" => (int)$tpsDT->format("w"),
        "hour" => (int)$tpsDT->format("G"),
        "minute" => (int)$tpsDT->format("i"),
        "second" => (int)$tpsDT->format("s"),
        "ms" => $ms % 1000,
    ]
    ];
    
    } else {

        if (!isset($tpss[$tUID]['cUID'])){
            $tpss[$tUID]['cUID'] = [];
        }

        if (!in_array($cUID, $tpss[$tUID]['cUID'])){
            $tpss[$tUID]['cUID'][] = $cUID;
        }

        if (!isset($tpss[$tUID]['import_unix'])){
            $tpss[$tUID]['import_unix'] = [];
        }

        if (!in_array($cUID, $tpss[$tUID])){
            $tpss[$tUID]['import_unix'][] = time();
        }
    }



    file_put_contents($tpsReport, json_encode($tpss, JSON_PRETTY_PRINT));
    }

//==============================================================================================
function json_environment(){

    $SITE = $GLOBALS['SITE'];
    return [
            "sys_slug" => $GLOBALS[$SITE]['SYS_SLUG'], 
            "sys_display" => $GLOBALS[$SITE]['SYS_DISPLAY'], 
            "dom_slug" => $GLOBALS[$SITE]['DOM_SLUG'], 
            "dom_display" => $GLOBALS[$SITE]['DOM_DISPLAY'], 
            "room_slug" => $GLOBALS[$SITE]['ROOM_SLUG'], 
            "room_display" => $GLOBALS[$SITE]['ROOM_DISPLAY'], 
            "mod_slug" => $GLOBALS[$SITE]['MOD_SLUG'], 
            "mod_display" => $GLOBALS[$SITE]['MOD_DISPLAY'], 
    ];
}


//==============================================================================================
function json_origin(){

    return [
        "material" => [ 
            "type" => $GLOBALS['MATERIAL']['TYPE'], 
            "source" => [
                "name" =>  $GLOBALS['MATERIAL']['SOURCE']['NAME'],
                "id" => $GLOBALS['MATERIAL']['SOURCE']['ID'],
                "created_on" => $GLOBALS['MATERIAL']['SOURCE']['CREATED'],
                "last_modified" => $GLOBALS['MATERIAL']['SOURCE']['LAST_MODIFIED'],
                ],
        "links" => $GLOBALS['MATERIAL']['REFS'],
        "notes" => $GLOBALS['MATERIAL']['DETAILS'],
        ]
    ];
}


//==============================================================================================
function buildCHEST($unix, $event_time){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    
    
    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';
    $RAW_TAGS = str_replace(["\r","\n", "\t"], '', $RAW_TAGS);
    $RAW_TAGS = trim($RAW_TAGS);
        $TAGS = tagSPLICER($RAW_TAGS);

    return [
        "c_version" => 4,
        "viewport" => $GLOBALS['PV'] ?? "::the.woman.on.K.st::",
        "assistant" => json_tool(),
        "payload" => json_payload(),
        "route" => json_route(),
        "tags" => $TAGS,
        "tags_metadata" => [
            "raw_tags" => $RAW_TAGS,
            "tag_parser" => 'charlieTHREADS',
            "parser_version" => 1
        ],
        "notes" => [],
        "import_env" => json_environment(),
        "ref_material" => json_origin(),
        "tps" => [
            "tUID" => $tUID, 
            "ingest_unix" => $unix,
            "event_unix" => $event_time,
            "timezone" => $_POST['POST__TZ'],
        ]
    ];
}

function demoCHEST($unix, $event_time){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    
    
    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';
    $RAW_TAGS = str_replace(["\r","\n", "\t"], '', $RAW_TAGS);
    $RAW_TAGS = trim($RAW_TAGS);
        $TAGS = tagSPLICER($RAW_TAGS);

    return [
        "c_version" => 4,
        "viewport" => $GLOBALS['PV'] ?? "::the.woman.on.K.st::",
        "assistant" => json_tool(),
        "payload" => json_payload(),
        "route" => json_route(),
        "tags" => $TAGS,
        "tags_metadata" => [
            "raw_tags" => $RAW_TAGS,
            "tag_parser" => 'charlieTHREADS',
            "parser_version" => 1
        ],
        "notes" => [],
        "import_env" => json_environment(),
        "ref_material" => "HIDDEN ON DEMO",
        "tps" => [
            "tUID" => $tUID, 
            "ingest_unix" => $unix,
            "event_unix" => $event_time,
            "timezone" => $_POST['POST__TZ'],
        ]
    ];
}
//----------------------------------------------------------------------------------------------------
function chestersCRATES($sha_env, $event_time, $unix){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE];

    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';

    $tpsDT = new DateTime("@$event_time");
    $tpsDT->setTimezone(new DateTimeZone("UTC"));
    $year = (int)$tpsDT->format('x');
    $date = (int)$tpsDT->format('x-m-d');

    $route = ROUTE('d', $sha_env);
    $BUILD_CHEST = buildCHEST($unix, $event_time);

    $router_1 = $route . $a['URI'] . '/';
     aleph($router_1);

    $router_2 = $route . '_CHESTER/search_by_crate/sort_by_event/' . $tpsDT->format('Y') . '/';
     aleph($router_2);

    $router_3 = $route . '_CHESTER/search_by_crate/sort_by_ingest/' . date('Y') . '/';
     aleph($router_3);

    $CHEST = $router_1 . $a['DOM_SLUG'] . '-' . $a['ROOM_SLUG'] . '.' . $GLOBALS['TOOL']['TYPE'] . '.json';    
    $ECHO_CHEST = $router_2 . $tpsDT->format('Y-m-d') . '.event.echo.json';
    $IM_ECHO_CHEST = $router_3 . date('Y-m-d') . '.ingest.echo.json';

     $json = file_get_contents($CHEST);
     $ECHO_json = file_get_contents($ECHO_CHEST);
     $IM_ECHO_json = file_get_contents($IM_ECHO_CHEST);

     $CHEST_THINGS = json_decode($json, true);
     $ECHO_CHEST_THINGS = json_decode($ECHO_json, true);
     $IM_ECHO_CHEST_THINGS = json_decode($IM_ECHO_json, true);
    
    if (!$CHEST_THINGS) { $CHEST_THINGS = []; }
        $CHEST_THINGS[$cUID] = $BUILD_CHEST;
    
    if (!$ECHO_CHEST_THINGS) { $ECHO_CHEST_THINGS = []; }
        $ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;
  
    if (!$IM_ECHO_CHEST_THINGS) { $IM_ECHO_CHEST_THINGS = []; }
        $IM_ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;
    
    file_put_contents($CHEST, json_encode($CHEST_THINGS, JSON_PRETTY_PRINT));
    file_put_contents($ECHO_CHEST, json_encode($ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));
    file_put_contents($IM_ECHO_CHEST, json_encode($IM_ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));

}

function demoSHIPMENT($sha_env, $event_time, $unix){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE];

    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';

    $tpsDT = new DateTime("@$event_time");
    $tpsDT->setTimezone(new DateTimeZone("UTC"));
    $year = (int)$tpsDT->format('x');
    $date = (int)$tpsDT->format('x-m-d');

    $route = ROUTE('d', $sha_env);
    $BUILD_CHEST = demoCHEST($unix, $event_time);

    $router_1 = $route . $a['URI'] . '/';
     aleph($router_1);

    $router_2 = $route . 'DEMO/_CHESTER/search_by_crate/sort_by_event/' . $tpsDT->format('Y') . '/';
     aleph($router_2);

    $router_3 = $route . 'DEMO/_CHESTER/search_by_crate/sort_by_ingest/' . date('Y') . '/';
     aleph($router_3);

    
    $CHEST = $router_1 . $a['DOM_SLUG'] . '-' . $a['ROOM_SLUG'] . '.' . $GLOBALS['TOOL']['TYPE'] . '.json';    
    $ECHO_CHEST = $router_2 . $tpsDT->format('Y-m-d') . '.event.echo.json';
    $IM_ECHO_CHEST = $router_3 . date('Y-m-d') . '.ingest.echo.json';

    if (file_exists($CHEST)) {
     $json = file_get_contents($CHEST);
     $ECHO_json = file_get_contents($ECHO_CHEST);
     $IM_ECHO_json = file_get_contents($IM_ECHO_CHEST);

     $CHEST_THINGS = json_decode($json, true);
     $ECHO_CHEST_THINGS = json_decode($ECHO_json, true);
     $IM_ECHO_CHEST_THINGS = json_decode($IM_ECHO_json, true);
    } 
    
    if (!$CHEST_THINGS) { $CHEST_THINGS = []; }
        $CHEST_THINGS[$cUID] = $BUILD_CHEST;
    
    if (!$ECHO_CHEST_THINGS) { $ECHO_CHEST_THINGS = []; }
        $ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;
  
    if (!$IM_ECHO_CHEST_THINGS) { $IM_ECHO_CHEST_THINGS = []; }
        $IM_ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;
    
    file_put_contents($CHEST, json_encode($CHEST_THINGS, JSON_PRETTY_PRINT));
    file_put_contents($ECHO_CHEST, json_encode($ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));
    file_put_contents($IM_ECHO_CHEST, json_encode($IM_ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));

}
//==============================================================================================
function json_tool(){

$TOOL = $GLOBALS['TOOL'];
    return [
        "name" => $TOOL['NAME'],
        "function" => $TOOL['FUNCTION'],
        "type" => $TOOL['TYPE'],
        "version" => $TOOL['VERSION'],
    ];
}

//==============================================================================================
function catalogUNIX($sha_env, $tpstime){

    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $MOD = $GLOBALS[$SITE]['MOD_SLUG'];

    $tpsDT = new DateTime("@$tpstime");
    $tpsDT->setTimezone(new DateTimeZone("UTC"));
    $year = (int)$tpsDT->format('x');
    $date = (int)$tpsDT->format('x-m-d');

    $block = intdiv((int)$tpstime, 10000);

    //--## router settings ------- ##

    $ROUTE__LINE = ROUTE('d', $sha_env);
    $ROUTE = $ROUTE__LINE . '/_DEWEY/lookup/by_tps/' . $year . '/' . $block  . '-block/';
    if (!is_dir($ROUTE)) { mkdir($ROUTE, 0775, true); }   

    $UNIX_CHEST = $ROUTE . $block . '.lookup.json';
    $json = file_get_contents($UNIX_CHEST);
    $payload = json_decode($json, true);

  //------## unix filler ------- ##
    if (!$payload){
        $payload ?? [];
    }

    if (!isset($payload[$tpstime][$MOD][$cUID])) $payload[$tpstime][$MOD][] = $cUID;
    

  //--## fill that crate! ------- ##
    file_put_contents($UNIX_CHEST, json_encode($payload));
}

//==============================================================================================
function charlieCATALOG($sha_env, $group, $add, $level){

    $router_1 = ROUTE('d', $sha_env);
    $router_2 = ROUTE('b', $sha_env);
        $catalog_rt = $router_1 . '_DEWEY/catalogs/';
        $catalog_rt2 = $router_2 . 'DEWEY/catalogs/';

          aleph($catalog_rt);
          aleph($catalog_rt2);

          $obj_catalog = $catalog_rt . $group. '.tag.catalog.json';
          $obj_catalog2 = $catalog_rt2 . $group. '.tag.catalog.json';
          $oc = json_decode(file_get_contents($obj_catalog), true);

        foreach ($add as $entity => $objs){
        foreach ($objs as $objects => $tags){
        foreach ($tags as $tag){

        if (!$oc) {
            $oc['count'] = 0;
        }
            $oc[$group][$$level]++;
            $oc['count']++;
        }
        }
        }
    
    file_put_contents($obj_catalog, json_encode($oc, JSON_PRETTY_PRINT));
    file_put_contents($obj_catalog2, json_encode($oc, JSON_PRETTY_PRINT));

}

function demoCATALOG($sha_env, $group, $add, $level){

    $router_1 = ROUTE('d', $sha_env);
    $router_2 = ROUTE('b', $sha_env);
        $catalog_rt = $router_1 . 'DEMO/_DEWEY/catalogs/';
        $catalog_rt2 = $router_2 . 'DEMO/_DEWEY/catalogs/';

          aleph($catalog_rt);
          aleph($catalog_rt2);

          $obj_catalog = $catalog_rt . $group. '.tag.catalog.json';
          $obj_catalog2 = $catalog_rt2 . $group. '.tag.catalog.json';
          $oc = json_decode(file_get_contents($obj_catalog), true);

        foreach ($add as $entity => $objs){
        foreach ($objs as $objects => $tags){
        foreach ($tags as $tag){

        if (!$oc) {
            $oc['count'] = 0;
        }
            $oc[$group][$$level]++;
            $oc['count']++;
        }
        }
        }
    
    file_put_contents($obj_catalog, json_encode($oc, JSON_PRETTY_PRINT));
    file_put_contents($obj_catalog2, json_encode($oc, JSON_PRETTY_PRINT));

}


//--------------------------------------------------------------------------------
function charliesTHREADS($sha_env, $tpstime){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';

    $router_1 = ROUTE('d', $sha_env);
    $add = tagSPLICER($RAW_TAGS);

    charlieCATALOG($sha_env, 'a-node', $add, 'entity');
    charlieCATALOG($sha_env, 'b-node', $add, 'objects');
    charlieCATALOG($sha_env, 'c-node', $add, 'tag');
    charlieLOOKUP($tpstime, $sha_env, $add, 'entity','objects', 'tag');
    charlieLOOKUP($tpstime, $sha_env, $add, 'objects', 'entity', 'tag');
    charlieLOOKUP($tpstime, $sha_env, $add, 'tag', 'entity', 'objects');


    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){

        $catalog_rt = $router_1 . '_CHARLIE/threads/by_aven/';
            aleph($catalog_rt);
            $MTAG_CHEST = $catalog_rt . $entity . '.ven.json';
            $json1 = file_get_contents($MTAG_CHEST);
            $tc = json_decode($json1, true);

        
    if (!$tc) {
        $tc = [
            'VEN' => $entity,
            'GRAVITY' => 0,
            'ALIAS' => [],
            'NOTES' => [],
            'METADATA' => [],
        ];
    }

    if (!isset($tc['SHELF'][$object]))
    $tc['SHELF'][$object] = [
            'GRAVITY' => 0,
            'BIN' => []
            ];

    if (!isset($tc['SHELF'][$object]['BIN']))
    $tc['SHELF'][$object]['BIN'][$tag] = 0;

    $tc['SHELF'][$object]['GRAVITY']++;
    $tc['SHELF'][$object]['BIN'][$tag]++;
    $tc['GRAVITY']++;


    file_put_contents($MTAG_CHEST, json_encode($tc, JSON_PRETTY_PRINT));

            }
        }
    }



    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){


    $catalog_rt = $router_1 . '_CHARLIE/threads/by_relativity/';
            aleph($catalog_rt);
            $impact_catalog = $catalog_rt . $tag . '.rel.json';
            $json5 = file_get_contents($impact_catalog);
            $impact = json_decode($json5, true);

    
    if (!$impact) {
        $impact = [
            'VEN' => $tag,
            'GRAVITY' => 0,
            'METADATA' => [],
        ];
    }

    if (!isset($impact['SHELF'][$entity]))
    $impact['SHELF'][$entity] = [
            'GRAVITY' => 0,
            'BIN' => []
            ];


    if (!isset($tc['SHELF'][$entity]['BIN']))
    $tc['SHELF'][$entity]['BIN'][$object] = 0;

    $impact['SHELF'][$entity]['BIN'][$object]++;
    $impact['SHELF'][$entity]['GRAVITY']++;
    $impact['GRAVITY']++;


        file_put_contents($impact_catalog, json_encode($impact, JSON_PRETTY_PRINT));

            }
        }
    }

    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){


    $catalog_rt = $router_1 . '_CHARLIE/threads/by_insect/';
            aleph($catalog_rt);
            $impact2_catalog = $catalog_rt . $object . '.ins.json';
            $json5 = file_get_contents($impact2_catalog);
            $impac2 = json_decode($json5, true);
    if (!$impac2) {
        $impac2 = [
                'VEN' => $object,
                'GRAVITY' => 0,
                'METADATA' => [],
        ];
        
    }
            if (!isset($impac2['SHELF'][$entity]))
            $impac2['SHELF'][$entity] = [
                    'GRAVITY' => 0,
                    'BIN' => []
                    ];
                $impac2['GRAVITY']++;


                $impac2['SHELF'][$entity]['GRAVITY']++;
                $impac2['SHELF'][$entity]['BIN'][$tag]++;

        file_put_contents($impact2_catalog, json_encode($impac2, JSON_PRETTY_PRINT));

            }
        }
    }
    }

function demoTHREADS($sha_env, $tpstime){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';

    $router_1 = ROUTE('d', $sha_env);
    $add = tagSPLICER($RAW_TAGS);

    demoCATALOG($sha_env, 'a-node', $add, 'entity');
    demoCATALOG($sha_env, 'b-node', $add, 'objects');
    demoCATALOG($sha_env, 'c-node', $add, 'tag');
    demoLOOKUP($tpstime, $sha_env, $add, 'entity','objects', 'tag');
    demoLOOKUP($tpstime, $sha_env, $add, 'objects', 'entity', 'tag');
    demoLOOKUP($tpstime, $sha_env, $add, 'tag', 'entity', 'objects');


    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){

        $catalog_rt = $router_1 . 'DEMO/_CHARLIE/threads/by_aven/';
            aleph($catalog_rt);
            $MTAG_CHEST = $catalog_rt . $entity . '.ven.json';
            $json1 = file_get_contents($MTAG_CHEST);
            $tc = json_decode($json1, true);

        
    if (!$tc) {
        $tc = [
            'VEN' => $entity,
            'GRAVITY' => 0,
            'ALIAS' => [],
            'NOTES' => [],
            'METADATA' => [],
        ];
    }

    if (!isset($tc['SHELF'][$object]))
    $tc['SHELF'][$object] = [
            'GRAVITY' => 0,
            'BIN' => []
            ];

    if (!isset($tc['SHELF'][$object]['BIN']))
    $tc['SHELF'][$object]['BIN'][$tag] = 0;

    $tc['SHELF'][$object]['GRAVITY']++;
    $tc['SHELF'][$object]['BIN'][$tag]++;
    $tc['GRAVITY']++;


    file_put_contents($MTAG_CHEST, json_encode($tc, JSON_PRETTY_PRINT));

            }
        }
    }



    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){


    $catalog_rt = $router_1 . 'DEMO/_CHARLIE/threads/by_relativity/';
            aleph($catalog_rt);
            $impact_catalog = $catalog_rt . $tag . '.rel.json';
            $json5 = file_get_contents($impact_catalog);
            $impact = json_decode($json5, true);

    
    if (!$impact) {
        $impact = [
            'VEN' => $tag,
            'GRAVITY' => 0,
            'METADATA' => [],
        ];
    }

    if (!isset($impact['SHELF'][$entity]))
    $impact['SHELF'][$entity] = [
            'GRAVITY' => 0,
            'BIN' => []
            ];


    if (!isset($tc['SHELF'][$entity]['BIN']))
    $tc['SHELF'][$entity]['BIN'][$object] = 0;

    $impact['SHELF'][$entity]['BIN'][$object]++;
    $impact['SHELF'][$entity]['GRAVITY']++;
    $impact['GRAVITY']++;


        file_put_contents($impact_catalog, json_encode($impact, JSON_PRETTY_PRINT));

            }
        }
    }

    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){


    $catalog_rt = $router_1 . 'DEMO/_CHARLIE/threads/by_insect/';
            aleph($catalog_rt);
            $impact2_catalog = $catalog_rt . $object . '.ins.json';
            $json5 = file_get_contents($impact2_catalog);
            $impac2 = json_decode($json5, true);
    if (!$impac2) {
        $impac2 = [
                'VEN' => $object,
                'GRAVITY' => 0,
                'METADATA' => [],
        ];
        
    }
            if (!isset($impac2['SHELF'][$entity]))
            $impac2['SHELF'][$entity] = [
                    'GRAVITY' => 0,
                    'BIN' => []
                    ];
                $impac2['GRAVITY']++;


                $impac2['SHELF'][$entity]['GRAVITY']++;
                $impac2['SHELF'][$entity]['BIN'][$tag]++;

        file_put_contents($impact2_catalog, json_encode($impac2, JSON_PRETTY_PRINT));

            }
        }
    }
    }


//==============================================================================================
function catalogJUKEBOX($sha_env, $link, $artist, $song){
    //GET YOUR COMMONS! 
    $RAW_TAGS = $_POST['POST__TAGS'] ?? '';
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $id = $GLOBALS['JUKEID']; 
    $ACTOR = $GLOBALS['TOOL']['ACTOR'];


  //--## router settings ------- ##
    $ROUTE__LINE = ROUTE('d', $sha_env);

        $ROUTE = $ROUTE__LINE . '/_DEWEY/catalog/';
        if (!is_dir($ROUTE)) { mkdir($ROUTE, 0775, true); }   

        $TAG_CHEST = $ROUTE . $SITE['SYS_SLUG'] . '-' . $SITE['DOM_SLUG'] . '-songs.catalog.json';
        $json = file_get_contents($TAG_CHEST);
        $TAGS = json_decode($json, true);

  //--## tag parser settings ------- ##
  
     $add = tagSPLICER($RAW_TAGS);

    //------## tag filler ------- ##
        if (!is_array($TAGS)) {
            $TAGS= [];
        } 
        
        if (!isset($TAGS[$artist][$id])){
            $TAGS[$artist][$id] = [
                "total_plays" => 0,
                "song_title" => $song,
                "link" => $link,
                "tagged_as" => $add,
                "heard_by" => []
            ];
        }


        if (!is_array($TAGS[$artist][$id]['heard_by'][$ACTOR])){
            $TAGS[$artist][$id]['heard_by'][$ACTOR] = [
                'count' => 0,
                'played_in' => []
            ];
        }

    if (!in_array($cUID, $TAGS[$artist][$id]['heard_by'][$ACTOR]['played_in'])){
        $TAGS[$artist][$id]['heard_by'][$ACTOR]['played_in'][$cUID] = 'no';
        $TAGS[$artist][$id]['total_plays']++;
        $TAGS[$artist][$id]['heard_by'][$ACTOR]['count']++;
    }

//--## fill that crate! ------- ##
    file_put_contents($TAG_CHEST, json_encode($TAGS, JSON_PRETTY_PRINT));
}


function getJUKED($string){
    $string = strtolower($string);
    $string = trim($string);
    $string = preg_replace('/\s+/', '-', $string);
    return strip_tags($string);
} 

function tagSPLICER($RAW_TAGS){
    $cUID = $GLOBALS['cUID'];
    $add = [];
    
    $TAGS = array_filter(array_map(function($q){
        return strtolower(trim($q));
    }, 
    explode(';', $RAW_TAGS)));

    foreach ($TAGS as $TAG){

        $TAG = strtolower(trim($TAG));

        if (strpos($TAG, '*') !== false) {    
            [$type, $value] = explode('*', $TAG, 2);
            $type = trim($type);
            $value = trim($value);
        } else {
            $type = "";
        }


        if (strpos($value, '&') !== false) {
            $values = explode('&', $value);
        } else {
            $values = [trim($value)];
        }
        
        foreach ($values as $tag){
        
            if (strpos($tag, '>') !== false) {
                [$parent, $child] = explode('>', $tag, 2);
                $parent = [trim($parent)];
                $child = [trim($child)];
            } else {
                $parent = [trim($tag)];
                $child = "";

            }

            foreach ($parent as $v){


                if (!is_array($add[$type])){
                    $add[$type][$v] = [];
                }
                if (!in_array($v, $add[$type])){
                    $add[$type][$v] = [];
                }

                foreach ($child as $c){
                    if (strpos($c, ',') !== false) {
                        $kid = explode(',', trim($c));
                    } else {
                        $kid = [trim($c)];
                    }

                    foreach ($kid as $c){

                    if (!is_array($add[$type][$v])){
                        $add[$type][$v][] = trim($c);
                    }
                    if (!in_array($c, $add[$type][$v])){
                        $add[$type][$v][] = trim($c);
                    } 
                    }

                }
            }   
        }

    }

    return $add;


}

//

