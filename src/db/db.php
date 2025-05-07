<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "YWeather";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // PDO::MYSQL_ATTR_MAX_ALLOWED_PACKET is not a valid constant, removing it
        PDO::ATTR_TIMEOUT => 600, // 10 minutes
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);

    // Augmenter les limites pour les requêtes longues
    $pdo->exec("SET SESSION wait_timeout = 600");
    $pdo->exec("SET SESSION innodb_lock_wait_timeout = 600"); // 10 minutes in seconds

    // Désactiver le mode STRICT pour éviter des erreurs potentielles
    $pdo->exec("SET sql_mode = ''");

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

