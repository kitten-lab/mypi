<?php

$GLOBALS['sonar'] = realpath(__DIR__ . '/../../') . '/';
require_once $sonar . 'easyRoutes.php';
require_once $sonar . $k['systems'] . 'invokeSky.php';
require_once $sonar . $k['config'] . 'env_config.php';

require_once __DIR__ .  '/-SKY_SIG-{{WORLD_NAME}}.php';
require_once $sonar . 'complexRoutes.php';

$SYSTEM_PATH = $sonar . $a[$sys];
getSkyAUTH($SYSTEM_PATH, $c, $sonar, $sys);
include $sonar . $c[$sys] . '--SIG--' . $site . '.php';

?>