<?php 

require_once "../src/db/db.php";

function getWeatherByPlaceAndTime($pdo, int $placeId, int $timeId): ?array {
    $sql = "
        SELECT weather.*
        FROM place_time_weather
        INNER JOIN weather ON place_time_weather.weather_id = weather.weather_id
        WHERE place_time_weather.place_id = :placeId AND place_time_weather.time_id = :timeId
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':placeId' => $placeId,
            ':timeId' => $timeId
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

function getLast24WeathersByPlace(PDO $pdo, int $placeId): array {
    $sql = "
        SELECT weather.*
        FROM place_time_weather
        INNER JOIN weather ON place_time_weather.weather_id = weather.weather_id
        INNER JOIN time ON place_time_weather.time_id = time.time_id
        WHERE place_time_weather.place_id = :placeId
        ORDER BY time.time_id DESC
        LIMIT 24
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':placeId' => $placeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

?>