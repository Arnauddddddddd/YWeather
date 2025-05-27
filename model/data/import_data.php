<?php
require_once "src/db/db.php";

function importDataToDB($pdo) {
    if (!file_exists('processed_data.csv')) {
        echo "Le fichier de données traitées n'existe pas!\n";
        return false;
    }
    
    // Start transaction for better performance
    $pdo->beginTransaction();
    
    $file = fopen('processed_data.csv', 'r');
    $header = fgetcsv($file, 0, ',');
    
    // Count total records more efficiently
    $totalRecords = countLinesInFile('processed_data.csv') - 1; // Minus header
    
    // Prepare column indexes once
    $colIndexes = [
        'commune' => array_search('commune', $header),
        'position' => array_search('position', $header),
        'forecast_timestamp' => array_search('forecast_timestamp', $header),
        'temperature_2m' => array_search('temperature_2m', $header),
        'humidity_2m' => array_search('humidity_2m', $header),
        'total_precipitation' => array_search('total_precipitation', $header),
        'wind_speed_10m' => array_search('wind_speed_10m', $header)
    ];
    
    // Prepare statements once
    $stmts = prepareStatements($pdo);
    
    // Cache for lookups
    $placeCache = [];
    $timeCache = [];
    
    $successCount = 0;
    $errorCount = 0;
    $batchSize = 500;
    $batch = 0;
    
    // For time estimation
    $startTime = microtime(true);
    $lastUpdateTime = $startTime;
    $updateInterval = 2; // Update every 2 seconds
    
    while (($data = fgetcsv($file, 0, ',')) !== FALSE) {
        try {
            $placeId = getPlaceId($pdo, $data, $colIndexes, $stmts, $placeCache);
            $timeId = getTimeId($pdo, $data, $colIndexes, $stmts, $timeCache);
            $weatherId = insertWeather($pdo, $data, $colIndexes, $stmts);
            
            if ($placeId && $timeId && $weatherId) {
                $stmts['insert_ptw']->execute([
                    'time_id' => $timeId,
                    'place_id' => $placeId,
                    'weather_id' => $weatherId
                ]);
                $successCount++;
            }
            
            // Commit every $batchSize records
            if (++$batch % $batchSize === 0) {
                $pdo->commit();
                $pdo->beginTransaction();
                
                // Display progress and time estimation
                $currentTime = microtime(true);
                if ($currentTime - $lastUpdateTime >= $updateInterval) {
                    displayTimeEstimate($startTime, $successCount, $totalRecords);
                    $lastUpdateTime = $currentTime;
                }
            }
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
    
    // Commit remaining records
    $pdo->commit();
    fclose($file);
    
    $totalTime = round(microtime(true) - $startTime, 2);
    echo "Import terminé en $totalTime secondes! $successCount lignes importées, $errorCount erreurs.\n";
    return true;
}

/**
 * Display time estimate for the import process
 */
function displayTimeEstimate($startTime, $processed, $total) {
    $elapsedTime = microtime(true) - $startTime;
    $percentComplete = ($processed / $total) * 100;
    
    // Avoid division by zero
    if ($processed > 0) {
        $recordsPerSecond = $processed / $elapsedTime;
        $remainingRecords = $total - $processed;
        $estimatedRemainingTime = ($recordsPerSecond > 0) ? $remainingRecords / $recordsPerSecond : 0;
        
        $minutes = floor($estimatedRemainingTime / 60);
        $seconds = round($estimatedRemainingTime % 60);
        
        echo sprintf(
            "Progression : %d/%d (%0.1f%%) - %0.2f enregistrements/sec - Temps restant estimé : %d min %d sec\n",
            $processed,
            $total,
            $percentComplete,
            $recordsPerSecond,
            $minutes,
            $seconds
        );
    } else {
        echo "Calcul du temps restant...\n";
    }
}

function prepareStatements($pdo) {
    return [
        'select_place' => $pdo->prepare("SELECT place_id FROM place WHERE name = :name"),
        'insert_place' => $pdo->prepare("INSERT INTO place (name, latitude, longitude) VALUES (:name, :latitude, :longitude)"),
        'select_time' => $pdo->prepare("SELECT time_id FROM time WHERE day = :day AND hour = :hour"),
        'insert_time' => $pdo->prepare("INSERT INTO time (day, hour) VALUES (:day, :hour)"),
        'insert_weather' => $pdo->prepare("INSERT INTO weather (temperature, precipitation, state, wind, humidity) VALUES (:temp, :precip, :state, :wind, :humidity)"),
        'insert_ptw' => $pdo->prepare("INSERT INTO place_time_weather (time_id, place_id, weather_id) VALUES (:time_id, :place_id, :weather_id)")
    ];
}

function getPlaceId($pdo, $data, $colIndexes, $stmts, &$placeCache) {
    $commune = $data[$colIndexes['commune']];
    
    if (isset($placeCache[$commune])) {
        return $placeCache[$commune];
    }
    
    $stmts['select_place']->execute(['name' => $commune]);
    $result = $stmts['select_place']->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $placeCache[$commune] = $result['place_id'];
        return $result['place_id'];
    }
    
    $lat = 0;
    $long = 0;
    
    if ($colIndexes['position'] !== false) {
        $position = $data[$colIndexes['position']];
        $coords = explode(',', $position);
        if (count($coords) >= 2) {
            $lat = trim($coords[0]);
            $long = trim($coords[1]);
        }
    }
    
    $stmts['insert_place']->execute([
        'name' => $commune,
        'latitude' => $lat,
        'longitude' => $long
    ]);
    
    $placeId = $pdo->lastInsertId();
    $placeCache[$commune] = $placeId;
    return $placeId;
}

function getTimeId($pdo, $data, $colIndexes, $stmts, &$timeCache) {
    $timestamp = $data[$colIndexes['forecast_timestamp']];
    
    if (isset($timeCache[$timestamp])) {
        return $timeCache[$timestamp];
    }
    
    $dateTime = new DateTime($timestamp);
    $day = $dateTime->format('Y-m-d H:i:s');
    $hour = $dateTime->format('Y-m-d H:i:s');
    
    $stmts['select_time']->execute([
        'day' => $day,
        'hour' => $hour
    ]);
    $result = $stmts['select_time']->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $timeCache[$timestamp] = $result['time_id'];
        return $result['time_id'];
    }
    
    $stmts['insert_time']->execute([
        'day' => $day,
        'hour' => $hour
    ]);
    
    $timeId = $pdo->lastInsertId();
    $timeCache[$timestamp] = $timeId;
    return $timeId;
}

function insertWeather($pdo, $data, $colIndexes, $stmts) {
    $temp = ($colIndexes['temperature_2m'] !== false) ? $data[$colIndexes['temperature_2m']] : 0;
    $humidity = ($colIndexes['humidity_2m'] !== false) ? $data[$colIndexes['humidity_2m']] : 0;
    $precip = ($colIndexes['total_precipitation'] !== false) ? $data[$colIndexes['total_precipitation']] : 0;
    $wind = ($colIndexes['wind_speed_10m'] !== false) ? $data[$colIndexes['wind_speed_10m']] : 0;
    
    $state = "sunny";
    if ($precip > 1) {
        $state = "rainy";
    } else if ($precip > 0.1) {
        $state = "cloudy";
    }
    
    $stmts['insert_weather']->execute([
        'temp' => $temp,
        'precip' => $precip,
        'state' => $state,
        'wind' => $wind,
        'humidity' => $humidity
    ]);
    
    return $pdo->lastInsertId();
}

/**
 * Count lines in a file without loading the entire file into memory
 */
function countLinesInFile($filePath) {
    $lineCount = 0;
    $handle = fopen($filePath, "r");
    
    while (!feof($handle)) {
        $line = fgets($handle);
        if ($line !== false) {
            $lineCount++;
        }
    }
    
    fclose($handle);
    return $lineCount;
}

importDataToDB($pdo);