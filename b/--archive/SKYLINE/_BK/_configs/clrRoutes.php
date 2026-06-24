<?php
$GLOBALS['sonar'] = realpath(__DIR__ . "/../../../") . '/';
# remove /../ per dir up, add /../ per directory deeper

# only ABL edit below this line!!!!!!!!!!
require_once $sonar . 'k/configs/env_config.php';
require_once $sonar . 'k/configs/clrRouters.php';
require $sonar . $kinit . 'resolvers.php';

require_once 'asSelf_config.php';

$GLOBALS['GETS'] = [
    "actor" => [],
    "dressing" => [],
    "set" => []
];
?>