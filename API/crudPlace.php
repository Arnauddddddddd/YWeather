<?php 

require_once "../src/db/db.php";

/**
 * Récupère les informations météorologiques pour un lieu et un temps donné.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param int $placeId L'ID du lieu.
 * @param int $timeId L'ID du temps.
 * @return array|null Un tableau associatif contenant les données météorologiques, ou null si non trouvé ou en cas d'erreur.
 */
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

/**
 * Récupère les dernières données météorologiques pour un lieu spécifique.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param int $nbr Le nombre de dernières données météorologiques à récupérer.
 * @param int $placeId L'ID du lieu.
 * @return array Un tableau d'objets contenant les données météorologiques, ou un tableau vide en cas d'erreur.
 */
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

/**
 * Récupère la date la plus récente des données disponibles dans la table 'time'.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @return string|null La date la plus récente au format string, ou null si aucune donnée ou en cas d'erreur.
 */
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

/**
 * Récupère la date la plus récente des données météorologiques pour un lieu spécifique.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param int $placeId L'ID du lieu.
 * @return string|null La date la plus récente au format string pour le lieu spécifié, ou null si aucune donnée ou en cas d'erreur.
 */
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