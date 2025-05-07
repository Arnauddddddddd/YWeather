<?php


function sendCityWeather($cityName) {
    return json_encode([
        "status" => "success",
        "value" => $cityName,
    ]);
}


function getWeatherPredictions() {
    echo shell_exec("python3 C:\\xampp2\\htdocs\\YWeather\\data\\test.py 2>&1");
}

getWeatherPredictions();

?>