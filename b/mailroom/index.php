<?php
require_once __DIR__ . '/-SKY_AUTH-mailroom.php';

keyMaker();
lockAndKey();

getSkyAUTH(ROUTE_TO_SHELL);
