<?php 

require_once "crud.php";
require_once "crudPlace.php";
require_once "../src/db/db.php";

$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

$path = str_replace(dirname($script_name), '', $request_uri);
$segments = explode('/', trim($path, '/'));

$cityName = $segments[2];
$city = getPlace($pdo, $cityName);
$cityArray = json_decode($city, true)["value"][0] ?? null;
$cityId = (int) $cityArray["place_id"] ?? null;

var_dump(getLastWeathersByPlace($pdo, 24,$cityId)); // Example usage



?>