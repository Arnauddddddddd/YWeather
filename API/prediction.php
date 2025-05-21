<?php 

// $city = "Strasbourg";
// $date = "2025-05-22";
// $lat = 48.55;
// $lon = 1.85;
// $temp = 28.5;

function getPrediction($city, $date, $lat, $lon, $temp) {
    $cmd = escapeshellcmd("python3 ../data/prediction.py " . escapeshellarg($city) . " " . escapeshellarg($date) . " $lat $lon $temp");
    $output = shell_exec($cmd);
    $data = json_decode($output, true);
    return $data;    
}




?>