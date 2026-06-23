<?php
require_once $GLOBALS['INTERA']['SYSTEM'] . 'chestersCrates.php'; // CHEST CRATING SYSTEM

require_once __DIR__ . '/-SIG-skyGenesis.php'; // ASSISTANT SETTINGS
#require_once __DIR__ . '/-CRATE-soprBASIC.php'; // CRATE FILLER SETTINGS

require_once $GLOBALS['INTERA']['SYSTEM'] . 'shadowENVO.php';
    $IS_IT = $GLOBALS['TOOL']['SHADOWENVO'];
        $sha_env = shadowENVO($IS_IT);
            if ($IS_IT == true) {
                echo "<div class='sha_env'>shadow mode on</div>";
}

require_once __DIR__ . '/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $POST__SYS =         $_POST['POST__SYS'];
    $POST__DOM =         $_POST['POST__DOM'];
    $POST__MOD =         $_POST['POST__MOD'];
    $POST__PV =          $_GET['pv'] ?? '__UNDISCLOSED__';
    $POST__TIMEZONE =    $_POST['POST__TZ'];
    $POST__ACTING__AS =  $_POST['ACTING__AS'];

    $GLOBALS['VARIANT'] = "BASIC";    

//  BET ROUTE__LINE >>>>>>>>>>>>>>>>>>>>>>>>
$ROUTE__LINE = ROUTE("b", $sha_env);

    $ROUTE = $ROUTE__LINE . $_POST['SYS_SLUG'] . '/';
    
        if (is_dir($ROUTE)) { KDE($$KDE__ERROR_TYPE, $KDE__SOURCE, $KDE__ECHO_CHAIN, $KDE__ERROR);}
        else { mkdir($ROUTE, 0775, true); }
        
        $CREATED_SKY_AUTH = CREATE_SKY_AUTH();
        $SKY_AUTH = $ROUTE . '-SKY_AUTH-' . str_replace("/", "-", $_POST['SYS_SLUG']) . '.php';

    file_put_contents($SKY_AUTH, $CREATED_SKY_AUTH);

        $CREATED_SKY_SIG = CREATE_SKY_SIG();
        $SKY_SIG = $ROUTE . '-SKY_SIG-' . str_replace("/", "-", $_POST['SYS_SLUG']) . '.php';

    file_put_contents($SKY_SIG, $CREATED_SKY_SIG);

        $CREATED_INDEX = CREATE_INDEX();
        $INDEX = $ROUTE . 'index.php';

    file_put_contents($INDEX, $CREATED_INDEX);

//  KHAF ROUTE LINE >>>>>>>>>>>>>>>>>>>>>>>>
$ROUTE__LINE = ROUTE("c", $sha_env);

    $ROUTE = $ROUTE__LINE . $_POST['SYS_SLUG'] . '/';
    
        if (is_dir($ROUTE)) { KDE($KDE__ERROR_TYPE, $KDE__SOURCE, $KDE__ECHO_CHAIN, $KDE__ERROR);}
        else { mkdir($ROUTE, 0775, true); }
        
        $CREATED_WORLD_SIG = CREATE_WORLD_SIG();
        $WORLD_SIG = $ROUTE . '--SIG--' . str_replace("/", "-", $_POST['SYS_SLUG']) . '.php';

        $CREATED_ERROR_FIG = CREATE_ERROR_FIG();
        $ERROR_FIG = $ROUTE . '-FIG--routeErrors.php';

        $CREATED_NAV_FIG = CREATE_NAV_FIG();
        $NAV_FIG = $ROUTE . '-FIG--nav.php';
            
    file_put_contents($WORLD_SIG, $CREATED_WORLD_SIG);
    file_put_contents($ERROR_FIG, $CREATED_ERROR_FIG);
    file_put_contents($NAV_FIG, $CREATED_NAV_FIG);

//  MEM ROUTE LINE >>>>>>>>>>>>>>>>>>>>>>>>
$ROUTE__LINE = ROUTE("m", $sha_env);

    $ROUTE = $ROUTE__LINE . 'rooms/' . $_POST['SYS_SLUG'] .'/';

    $WELCOME_ROOM = $ROUTE . '/public/';
    $FIRST_ROOM = $ROUTE . $_POST['DOM_SLUG'] . '/';
    
        if (!is_dir($WELCOME_ROOM)) { mkdir($WELCOME_ROOM, 0775, true); }
        if (!is_dir($FIRST_ROOM)) { mkdir($FIRST_ROOM, 0775, true); }

        $CREATED_WELCOME_ROOM = CREATE_WELCOME_HOME();
        $WELCOME_HOME = $WELCOME_ROOM . 'hi-from-SKY.php';

        $CREATED_FIRST_ROOMKEY = CREATE_FIRST_ROOMKEY();
        $FIRST_ROOM = $FIRST_ROOM . $_POST['KEY_SLUG'] . '.php';

    file_put_contents($WELCOME_HOME, $CREATED_WELCOME_ROOM);
    file_put_contents($FIRST_ROOM, $CREATED_FIRST_ROOMKEY);

//  ALEPH ROUTE LINE >>>>>>>>>>>>>>>>>>>>>>>>
$ROUTE__LINE = ROUTE('a', $sha_env);

    $ROUTE = $ROUTE__LINE . $_POST['SYS_SLUG'] . '/asSys/';
    
        if (is_dir($ROUTE)) { KDE($$KDE__ERROR_TYPE, $KDE__SOURCE, $KDE__ECHO_CHAIN, $KDE__ERROR);}
        else { mkdir($ROUTE, 0775, true); }

        $CREATED_SHELL = CREATE_BASE_SHELL($GLOBALS['VARIANT']);
        $SHELL = $ROUTE . 'shell.php';

        $CREATED_HEADER = CREATE_BASE_HEADER($GLOBALS['VARIANT']);
        $HEADER = $ROUTE . 'header.php';

        $CREATED_FOOTER = CREATE_BASE_FOOTER($GLOBALS['VARIANT']);
        $FOOTER = $ROUTE . 'footer.php';

        $CREATED_NAV = CREATE_BASE_NAV($GLOBALS['VARIANT']);
        $NAV = $ROUTE . 'nav.php';

        $CREATED_STYLESHEET = CREATE_BASIC_STYLE($GLOBALS['VARIANT']);
        $STYLE = $ROUTE . 'style.css'; //empty sheet
            
    file_put_contents($SHELL, $CREATED_SHELL);
    file_put_contents($HEADER, $CREATED_HEADER);
    file_put_contents($FOOTER, $CREATED_FOOTER);
    file_put_contents($STYLE, $CREATED_STYLESHEET);
    file_put_contents($NAV, $CREATED_NAV);


}

  

    