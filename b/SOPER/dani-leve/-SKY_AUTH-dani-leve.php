<?php
$GLOBALS['sonar'] = realpath(__DIR__ . "/../../") . '/';
$GLOBALS['SONAR'] = realpath(__DIR__ . "/../../") . '/';

require_once $SONAR . 'easyRoutes.php';

require_once $GLOBALS['INTERA']['SYSTEM'] . "invokeSky.php";

require_once $INTERA['CONFIG'] . 'env_config.php';

require_once __DIR__ .  "/-SKY_SIG-dani-leve.php";

require_once $SONAR . 'complexRoutes.php';

$SHELL_ROUTE = $ROUTE['A'][$SYS];

getSkyAUTH($SHELL_ROUTE);

include $ROUTE['C'][$SYS] . '--SIG--' . $SYS . '.php';

?>