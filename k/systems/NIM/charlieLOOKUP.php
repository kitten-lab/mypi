
<?php
function charlieLOOKUP($tpstime, $sha_env, $add, $level,$level2,$level3){
    //GET YOUR COMMONS! 
    $tUID = $GLOBALS['tUID'];
    $cUID = $GLOBALS['cUID'];
    $SITE = $GLOBALS['SITE'];
    $MOD = $GLOBALS[$SITE]['MOD_SLUG'];

        foreach ($add as $entity => $objs){
        foreach ($objs as $objects => $tags){
        foreach ($tags as $tag){


    $router_1 = ROUTE('d', $sha_env);
        $catalog_rt = $router_1 . '_DEWEY/lookup/by_tag/';
          aleph($catalog_rt);
          $obj_catalog = $catalog_rt . $$level . '.lookup.json';
          $oc = json_decode(file_get_contents($obj_catalog), true);

        if (!$oc) {
            $oc = [];
        }

        if (!isset($oc['CABINET'][$$level])) {
            $oc['CABINET'][$$level] = [
                'METADATA' => [
                    'GRAVITY' => 0,
                    'INGEST' => [
                        'FIRST' => time(),  
                        'LAST' => [],
                    ],
                    'EVENT' => [
                        'FIRST' => [],
                        'LAST' => [],
                    ]           
                ]
            ];
        }

        if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2])) {
            $oc['CABINET'][$$level]['SHELF'][$$level2] = [
                'METADATA' => [
                    'GRAVITY' => 0,
                    'INGEST' => [
                        'FIRST' => time(),  
                        'LAST' => [],
                    ],
                    'EVENT' => [
                        'FIRST' => [],
                        'LAST' => [],
                    ],
                    'LAST_USAGE' => []                          
                ]
            ];
        }

        if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3])) {
            $oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3] = [
                'METADATA' => [
                    'GRAVITY' => 0,
                    'INGEST' => [
                        'FIRST' => time(),  
                        'LAST' => [],
                    ],
                    'EVENT' => [
                        'FIRST' => [],
                        'LAST' => [],
                    ] 
                ]
            ];
        }

            if (!isset($oc['CABINET'][$$level]['METADATA']['GRAVITY'])) $oc['CABINET'][$$level]['METADATA']['GRAVITY'] ?? [];
            $oc['CABINET'][$$level]['METADATA']['GRAVITY']++;

            if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['GRAVITY'])) $oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['GRAVITY'] ?? [];
            $oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['GRAVITY']++;

            if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['GRAVITY'])) $oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['METADATA']['GRAVITY'] ?? [];
            $oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['METADATA']['GRAVITY']++;
            
            if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['INSTANCES'][$tpstime][$MOD][$cUID]))
             $oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['INSTANCES'][$tpstime][$MOD][] = $cUID;
            
            if (!isset($oc['CABINET'][$$level]['METADATA']['LAST_USAGE'][$tpstime])) $oc['CABINET'][$$level]['METADATA']['LAST_USAGE'] = [];
            if (!isset($oc['CABINET'][$$level]['METADATA']['LAST_USAGE'][$tpstime][$$level2])) $oc['CABINET'][$$level]['METADATA']['LAST_USAGE'][$tpstime][] = $$level2;
            if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['LAST_USAGE'][$tpstime])) $oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['LAST_USAGE'] = [];
            if (!isset($oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['LAST_USAGE'][$tpstime][$$level3])) $oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['LAST_USAGE'][$tpstime][] = $$level3;

            $oc['CABINET'][$$level]['METADATA']['INGEST']['LAST'] = time();
            $oc['CABINET'][$$level]['SHELF'][$$level2]['METADATA']['INGEST']['LAST'] = time();
            $oc['CABINET'][$$level]['SHELF'][$$level2]['BIN'][$$level3]['METADATA']['INGEST']['LAST'] = time();

    file_put_contents($obj_catalog, json_encode($oc, JSON_PRETTY_PRINT));

        }
        }
        }
    

}