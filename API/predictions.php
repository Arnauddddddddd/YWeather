<?php

function getPredictionForCity($cityName, $temperature = null) {
    $currentTemp = $temperature ?? 20.0;
    $currentDate = date('Y-m-d');
    $currentHour = date('H');
    
    // Échapper les guillemets dans le nom de ville
    $safeCityName = escapeshellarg($cityName);
      // Commande Python pour prédiction (chemin relatif)
    $pythonPath = dirname(__DIR__) . "/model/prediction/prediction.py";
    $command = "python \"$pythonPath\" predict $currentDate $currentHour $safeCityName $currentTemp 2>&1";
    
    $output = shell_exec($command);
    $result = json_decode($output, true);
    
    if ($result && $result['status'] === 'success') {
        return $result['data'];
    }
    
    return null;
}

function getWeeklyPredictionForCity($cityName, $temperature = null) {
    $currentTemp = $temperature ?? 20.0;
    $currentDate = date('Y-m-d');
    $currentHour = date('H');
    
    // Échapper les guillemets dans le nom de ville
    $safeCityName = escapeshellarg($cityName);
      // Commande Python pour prédiction hebdomadaire (chemin relatif)
    $pythonPath = dirname(__DIR__) . "/model/prediction/prediction.py";
    $command = "python \"$pythonPath\" weekly $currentDate $currentHour $safeCityName $currentTemp 2>&1";
    
    $output = shell_exec($command);
    $result = json_decode($output, true);
    
    if ($result && $result['status'] === 'success') {
        return $result['data'];
    }
    
    return null;
}

?>