<?php 
// definers
define("POST_soperSECTION", $_POST['soper_section'] ?? "");
define("POST_soper_leaf", $_POST['soper_leaf'] ?? "");
define("POST_sectionID", $_POST['soper_leaf'] ?? "");

function json_payload(){
    return [
    "soper" => [
        "agent" => $_POST['agent'],
        "sec_display" => $GLOBALS['soperSECTION'],
        "sec_slug" => $GLOBALS['sectionID'],
        "supr_slug" => $GLOBALS['soperID'],
        "content" => $_POST['soper_leaf'],
    ]];
}

function json_route(){
    return [];
}

function soperSTORE(){

    //GET YOUR COMMONS! 
    $SITE = $GLOBALS['SITE'];
    $a = $GLOBALS[$SITE];


    $RAW_TAGS = POST_TAGS;
    $RAW_TAGS = str_replace(["\r","\n", "\t"], '', $RAW_TAGS);
    $RAW_TAGS = trim($RAW_TAGS);
        $TAGS = tagSPLICER($RAW_TAGS);
        
    $soperSECTION = $_POST['soper_section'];
    $soper_leaf = $_POST['soper_leaf'];

    $soperROUTER = ROUTE('d', SHADOW_TOGGLE);
        $soperFOLDER = $soperROUTER . $a['URI'] . '/';
          aleph($soperFOLDER);
          $soperSTACK = $soperFOLDER . '/' . $a['DOM_SLUG'] . '-' . $a['ROOM_SLUG'] . '.sopr.frags.json';
          $soperSTORE = json_decode(file_get_contents($soperSTACK), true);

        if (!$soperSTORE){
            $soperSTORE = [];
        }

        $sectionID = strtolower(preg_replace('/\s+/', '', $soperSECTION));
        $GLOBALS['sectionID'] = $sectionID;
        $GLOBALS['soperSECTION'] = $soperSECTION;
        
        if (!isset($soperSTORE['SECTION'][$sectionID])) {
            $soperSTORE['SECTION'][$sectionID] = [
                'LABEL' => $soperSECTION,
                'NOTES' => [],
                'SOPERS' => [],
            ];
        }

        $max = 0;

        foreach ($soperSTORE['SECTION'][$sectionID]['SOPERS'] as $soper){
            if (preg_match('/-(\d+)$/', $soper['ID'], $matches)){
                $num = intval($matches[1]);
                if ($num > $max) $max = $num;
            }
        }

        $nextID = $max + 1;
        $soperID = $sectionID
        . '-'
        . str_pad($nextID, 4, '0', STR_PAD_LEFT);

        $GLOBALS['soperID'] = $soperID;

        if (!in_array($soper_leaf, $soperSTORE['SECTION'][$sectionID]['SOPERS'])){
            $soperSTORE['SECTION'][$sectionID]['SOPERS'][] =  [
                'ID' => $soperID,
                'FRAG' => $soper_leaf,
                'AGENT' => $_POST['agent'],
                'METADATA' => [
                    'ADDED' => time(),
                    'cUID' => cUID,
                    'TAGS' => $TAGS,
                ]
    
            ];
        }

    file_put_contents($soperSTACK, json_encode($soperSTORE, JSON_PRETTY_PRINT));
    
}