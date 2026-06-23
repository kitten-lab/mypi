<?php 
require_once '_configs/clrRoutes.php';
$key = $_GET['key'];
if ($_GET['key'] == null){
    $key = 'home';
}
require __DIR__ . '/sky/' . $key . '.php';
require resolveShell($sys);
?>