<?php

define('echoSONAR', realpath(__DIR__ . '/../../') . '/');

require_once echoSONAR . 'easyRoutes.php';
require_once __DIR__ . '/-SKY_SIG-mailroom.php';
require_once echoSONAR . 'complexRoutes.php';
require_once ROUTE_TO_SYSTEMS . 'invokeSky.php';
require_once ROUTE_TO_CONFIGS . 'env_config.php';
require_once ROUTE_TO_LOCALCONFIG . '--SIG--mailroom.php';
