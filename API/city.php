<?php 

ob_start();
require_once "index.php";
ob_end_clean();

$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

$path = str_replace(dirname($script_name), '', $request_uri);
$segments = explode('/', trim($path, '/'));

$cityName = $segments[2];
echo get($pdo, $cityName);

?>