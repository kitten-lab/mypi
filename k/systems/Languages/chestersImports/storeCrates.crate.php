<?php 

//----------------------------------------------------------------------------------------------------
function chestersCRATES(){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE];

    $tpsDT = TPS_EVENTCALC;
    $tpsDT->setTimezone(new DateTimeZone("UTC"));

    $BUILD_CHEST = buildCHEST();

  // PLACE FILE LOCALLY TO SURFACE
    $route = ROUTE('d', SHADOW_TOGGLE);
    $router_1 = $route . BLOCK_URI . '/';
     aleph($router_1);

    $CHEST = $router_1 . $a['DOM_SLUG'] . '-' . $a['ROOM_SLUG'] . '.' . $GLOBALS['TOOL']['TYPE'] . '.json';    
     $json = file_get_contents($CHEST);
     $CHEST_THINGS = json_decode($json, true);

    if (!$CHEST_THINGS) { $CHEST_THINGS = []; }
        $CHEST_THINGS[$cUID] = $BUILD_CHEST;

    file_put_contents($CHEST, json_encode($CHEST_THINGS, JSON_PRETTY_PRINT));

  // LONG STORAGE ROUTING
    $router_2 = ROUTE_TO_CHESTER . 'sort_by_event/' . $tpsDT->format('Y') . '/';
     aleph($router_2);

    $ECHO_CHEST = $router_2 . $tpsDT->format('Y-m-d') . '.event.echo.json';
     $ECHO_json = file_get_contents($ECHO_CHEST);
     $ECHO_CHEST_THINGS = json_decode($ECHO_json, true);
    
    if (!$ECHO_CHEST_THINGS) { $ECHO_CHEST_THINGS = []; }
        $ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;

    file_put_contents($ECHO_CHEST, json_encode($ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));


    $router_3 = ROUTE_TO_CHESTER . 'sort_by_ingest/' . date('Y') . '/';
     aleph($router_3);

    $IM_ECHO_CHEST = $router_3 . date('Y-m-d') . '.ingest.echo.json';
     $IM_ECHO_json = file_get_contents($IM_ECHO_CHEST);
     $IM_ECHO_CHEST_THINGS = json_decode($IM_ECHO_json, true);
    
    if (!$IM_ECHO_CHEST_THINGS) { $IM_ECHO_CHEST_THINGS = []; }
        $IM_ECHO_CHEST_THINGS[$cUID] = $BUILD_CHEST;

    file_put_contents($IM_ECHO_CHEST, json_encode($IM_ECHO_CHEST_THINGS, JSON_PRETTY_PRINT));
}

//==============================================================================================
function buildCHEST(){

    $RAW_TAGS = POST_TAGS;
    $RAW_TAGS = str_replace(["\r","\n", "\t"], '', $RAW_TAGS);
    $RAW_TAGS = trim($RAW_TAGS);
        $TAGS = tagSPLICER($RAW_TAGS);

    return [
        "c_version" => 4,
        "viewport" => POST_PV,
        "assistant" => json_tool(),
        "payload" => json_payload(),
        "route" => json_route(),
        "tags" => $TAGS,
        "tags_metadata" => [
            "raw_tags" => POST_TAGS,
            "tag_parser" => 'charlieTHREADS',
            "parser_version" => 1
        ],
        "notes" => [],
        "import_env" => json_environment(),
        "ref_material" => json_origin(),
        "tps" => [
            "tUID" => tUID, 
            "ingest_unix" => TPS_UNIX,
            "event_unix" => TPS_EVENTTIME,
            "timezone" => TPS_TIMEZONE,
        ]
    ];
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