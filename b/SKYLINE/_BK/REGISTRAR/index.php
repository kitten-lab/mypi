<?php 
// IMPORT-TERMINAL BASE ꓘra *|*>>> "Alice through the looking glass" //
require_once '_configs/clrRoutes.php';
require_once '_configs/asSelf_config.php';

$pageTitle = "THE SKYLINE ON-INTERA";
$pageLogic = getTool('ven.registrar', 'actorAdd');
$pageSlug = getTool('ven.registrar', 'pageAdd');

require resolveShell($sys);
?>