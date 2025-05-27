<?php
require_once "../src/db/db.php";

/**
 * Récupère les informations sur les lieux (places) depuis la base de données.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param mixed $place Peut être vide (pour toutes les places), un ID numérique de place, ou un nom de place.
 * @return string Un tableau JSON contenant les informations sur les places ou un message d'erreur.
 */
function getPlace( $pdo, $place ) {
    if ( empty( $place ) ) {
        // Si $place est vide, récupérer toutes les places.
        $stmt = $pdo->prepare("SELECT * FROM Place");
        $stmt->execute();
    } else if ( is_numeric( $place ) ) {
        // Si $place est un nombre (ID), récupérer la place par son ID.
        $stmt = $pdo->prepare("SELECT * FROM Place WHERE place_id = :place_id");
        $stmt->execute( [ 'place_id' => $place ] );
    } else {
        // Sinon, considérer $place comme un nom et le décoder pour récupérer la place par son nom.
        $place = urldecode($place);
        $stmt = $pdo->prepare("SELECT * FROM Place WHERE name = :name");
        $stmt->execute( [ 'name' => $place ] );
    }
    
    $places = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    return json_encode([
        "status" => "success",
        "value" => $places,
    ]);
}

/**
 * Gère les requêtes POST pour ajouter une nouvelle place à la base de données.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @return void
 */
function post( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    if ($data !== null) {
        // Récupérer les données du JSON et insérer une nouvelle place.
        $name = $data['name'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $stmt = $pdo->prepare("INSERT INTO place (name, latitude, longitude) VALUES (:name, :latitude, :longitude)");
        $stmt->execute( [ 'name' => $name, 'latitude' => $latitude, 'longitude' => $longitude ] );
        http_response_code(200);
        echo json_encode([
            "status" => "success",
        ]);
    } else {
        // Gérer les données JSON invalides.
        http_response_code(400);
        echo "Invalid JSON data";
    }
}

/**
 * Gère les requêtes PUT pour mettre à jour une place existante dans la base de données.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @return void
 */
function put( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if ($data !== null) {
        // Construire la requête de mise à jour en fonction des champs fournis.
        $id = $data['id'];
        $name = $data['name'] ?? null;
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;
        $setClauses = [];
        $params = [];

        if ($name !== null) {
            $setClauses[] = "name = :name";
            $params[':name'] = $name;
        }
        if ($latitude !== null) {
            $setClauses[] = "latitude = :latitude";
            $params[':latitude'] = $latitude;
        }
        if ($longitude !== null) {
            $setClauses[] = "longitude = :longitude";
            $params[':longitude'] = $longitude;
        }

        if (empty($setClauses)) {
            http_response_code(400);
            echo "No data provided for update";
            return;
        }

        $sql = "UPDATE place SET " . implode(', ', $setClauses) . " WHERE place_id = :id";
        $params[':id'] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        http_response_code(200);
        echo json_encode([
            "status" => "success",
        ]);
    } else {
        // Gérer les données JSON invalides.
        http_response_code(400);
        echo "Invalid JSON data";
    }
}

/**
 * Gère les requêtes DELETE pour supprimer une place de la base de données.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @return string Un tableau JSON indiquant le succès ou l'échec de l'opération.
 */
function remove( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    $id = $data['id'];
    $stmt = $pdo->prepare("DELETE FROM place WHERE place_id = :id"); // Changed 'id' to 'place_id' assuming it's the primary key
    $stmt->execute( [ 'id' => $id ] );
    
    http_response_code(200);
    return json_encode([
        "status" => "success",
    ]);
}

/**
 * Suggère des noms de lieux basés sur un début de chaîne.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param string $start Le début de la chaîne de recherche.
 * @return string Un tableau JSON contenant les suggestions de noms de lieux.
 */
function suggest($pdo, $start) {
    $stmt = $pdo->prepare("SELECT name FROM Place WHERE name LIKE :start LIMIT 5");
    $stmt->execute(['start' => $start . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    return json_encode([
        "status" => "success",
        "value" => $results
    ]);
}

$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Extrait le chemin de la requête en retirant le nom du script.
$path = str_replace(dirname($script_name), '', $request_uri);
// Sépare le chemin en segments.
$segments = explode('/', trim($path, '/'));
if (isset($segments[1])) {
    $city = $segments[1]; // Cette variable semble inutilisée dans la fonction processRequest actuelle.
}

/**
 * Traite la requête HTTP en fonction de la méthode (GET, POST, PUT, DELETE) et des segments d'URL.
 *
 * @param PDO $pdo L'objet PDO pour la connexion à la base de données.
 * @param array $segments Les segments de l'URL de la requête.
 */
function processRequest($pdo, $segments) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($segments[1]) && $segments[1] == "suggest") {
                // Si le segment est "suggest", appeler la fonction de suggestion.
                echo suggest($pdo, $segments[2] ?? ''); // Utiliser l'opérateur de coalescence null pour $segments[2]
            } else {
                // Sinon, appeler la fonction getPlace avec le segment comme paramètre.
                echo getPlace($pdo, $segments[1] ?? null); // Utiliser l'opérateur de coalescence null pour $segments[1]
            }
            break;
        case 'POST':
            // Gérer les requêtes POST.
            echo post( $pdo );
            break;
        case 'PUT':
            // Gérer les requêtes PUT.
            echo put( $pdo );
            break;
        case 'DELETE':
            // Gérer les requêtes DELETE.
            echo remove( $pdo );
            break;  
        default:
            // Gérer les méthodes de requête non supportées.
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid Request",
            ]);
            break;
    }
}

// Vérifie si le script est exécuté directement et non inclus comme une bibliothèque.
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    // Appelle la fonction principale pour traiter la requête.
    echo processRequest($pdo, $segments);
}

?>