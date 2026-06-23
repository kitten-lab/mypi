<?php 
require __DIR__ . '/NIM/charlieLOOKUP.php';
require __DIR__ . '/NIM/demoLOOKUP.php';
require __DIR__ . '/Languages/chestersImports/tpsReports.crate.php';
require __DIR__ . '/Languages/chestersImports/storeCrates.crate.php';
require __DIR__ . '/Languages/chestersImports/getJuked.crate.php';
require __DIR__ . '/Languages/chestersImports/catalogKing.crate.php';

function aleph($ROUTE){
    // aleph is the ox. it plow the field if there is no directory, so space can exist //
    if (!is_dir($ROUTE)) { mkdir($ROUTE, 0775, true); }
}

function SKY_GET_tUID(){
    define("tUID", TPS_EVENTTIME . '.tps');

    //Phase out later
    $GLOBALS['tUID'] = tUID;
}

function SKY_GET_cUID(){
  define("cUID", 'crate.' . strtoupper(bin2hex(random_bytes(8))));

  //Phase out later
  $GLOBALS['cUID'] = cUID;
}


//

