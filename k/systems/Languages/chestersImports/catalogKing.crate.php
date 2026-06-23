<?php

//==============================================================================================
function catalogUNIX(){

    $block = intdiv((int)TPS_TPSTIME, 10000);

    //--## router settings ------- ##
    $ROUTE = ROUTE_TO_DEWEY_LOOKUP . 'by_tps/' . TPS_SYEAR . '/' . $block  . '-block/';
      aleph($ROUTE);

    $UNIX_CHEST = $ROUTE . $block . '.lookup.json';
    $json = file_get_contents($UNIX_CHEST);
    $payload = json_decode($json, true);

  //------## unix filler ------- ##
    if (!$payload){
        $payload ?? [];
    }

    if (!isset($payload[TPS_TPSTIME][MOD_SLUG][cUID])) $payload[TPS_TIMEZONE][MOD_SLUG][] = cUID;
    
  //--## fill that crate! ------- ##
    file_put_contents($UNIX_CHEST, json_encode($payload));
}

//==============================================================================================
function charlieCATALOG($group, $add, $level){
  $catalog_rt = ROUTE_TO_DEWEY_CATALOG_D;
    aleph($catalog_rt);

  $obj_catalog = $catalog_rt . $group. '.tag.catalog.json';
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
}

//--------------------------------------------------------------------------------
function charliesTHREADS(){

    $add = tagSPLICER();

    charlieCATALOG('a-node', $add, 'entity');
    charlieCATALOG('b-node', $add, 'objects');
    charlieCATALOG('c-node', $add, 'tag');

    charlieLOOKUP(TPS_TPSTIME, SHADOW_TOGGLE, $add, 'entity','objects', 'tag');
    charlieLOOKUP(TPS_TPSTIME, SHADOW_TOGGLE, $add, 'objects', 'entity', 'tag');
    charlieLOOKUP(TPS_TPSTIME, SHADOW_TOGGLE, $add, 'tag', 'entity', 'objects');


    foreach ($add as $entity => $objs){
        foreach ($objs as $object => $tags){
            foreach ($tags as $tag){

            $catalog_rt = ROUTE_TO_CHARLIE . 'by_aven/';
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

            $catalog_rt = ROUTE_TO_CHARLIE . 'by_relativity/';
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


        $catalog_rt = ROUTE_TO_CHARLIE . 'threads/by_insect/';
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


function tagSPLICER(){

    $add = [];
    
    $TAGS = array_filter(array_map(function($q){
        return strtolower(trim($q));
    }, 
    explode(';', POST_TAGS)));

    foreach ($TAGS as $TAG){

        $TAG = strtolower(trim($TAG));

        if (strpos($TAG, '*') !== false) {    
            [$type, $value] = explode('*', $TAG, 2);
            $type = trim($type);
            $value = trim($value);
        } else {
            $type = "";
            $value = null;
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