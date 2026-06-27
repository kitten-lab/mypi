<?php

define("echoSONAR", realpath(__DIR__ . "/../../") . '/');

require_once echoSONAR . 'easyRoutes.php';
require_once __DIR__ .  "/-SKY_SIG-nim.php";
require_once echoSONAR . 'complexRoutes.php';
require_once ROUTE_TO_SYSTEMS . "invokeSky.php";
require_once ROUTE_TO_CONFIGS . 'env_config.php';
require_once ROUTE_TO_LOCALCONFIG . '--SIG--nim.php';

?>