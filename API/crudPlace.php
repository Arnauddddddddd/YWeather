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

function getLastWeathersByPlace(PDO $pdo, int $nbr, int $placeId): array {
    $nbr = (int)$nbr;
    $sql = "
        SELECT weather.*
        FROM place_time_weather
        INNER JOIN weather ON place_time_weather.weather_id = weather.weather_id
        INNER JOIN time ON place_time_weather.time_id = time.time_id
        WHERE place_time_weather.place_id = :placeId
        ORDER BY time.time_id DESC
        LIMIT $nbr
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':placeId' => $placeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getLatestDataDate(PDO $pdo): ?string {
    $sql = "
        SELECT MAX(time.hour) as latest_date
        FROM time
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['latest_date'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

function getLatestDataDateForPlace(PDO $pdo, int $placeId): ?string {
    $sql = "
        SELECT MAX(time.hour) as latest_date
        FROM place_time_weather
        INNER JOIN time ON place_time_weather.time_id = time.time_id
        WHERE place_time_weather.place_id = :placeId
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':placeId' => $placeId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['latest_date'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

?>