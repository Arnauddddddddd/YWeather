<?php
require_once "../src/db/db.php";

header("Content-Type: application/json");



function getPlace( $pdo, $place ) {
    if ( empty( $place ) ) {
        $stmt = $pdo->prepare("SELECT * FROM Place");
        $stmt->execute();
    } else if ( $place == (int) $place ) {
        $stmt = $pdo->prepare("SELECT * FROM Place WHERE place_id = :place_id");
        $stmt->execute( [ 'place_id' => $place ] );
    } else {
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

function post( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    if ($data !== null) {
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
        http_response_code(400);
        echo "Invalid JSON data";
     }
}


function put( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if ($data !== null) {
        $id = $data['id'];
        $name = $data['name'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $str = "SET ";
        if ($name != null) {
            $str = $str . "name = $name,";
        }
        if ($latitude != null) {
            $str = $str . "latitude = $latitude,";
        }
        if ($longitude != null) {
            $str = $str . "longitude = $longitude,";
        }
        $str = substr_replace($str, '', -1);
        $stmt = $pdo->prepare("UPDATE place " . $str ." WHERE id = $id");
        $stmt->execute();
        http_response_code(200);
        echo json_encode([
            "status" => "success",
        ]);
     } else {
        http_response_code(400);
        echo "Invalid JSON data";
     }
}

function remove( $pdo ) {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    $stmt = $pdo->prepare("DELETE FROM place WHERE id = :id");
    $id = $data['id'];
    $stmt->execute( [ 'id' => $id ] );
    
    http_response_code(200);
    return json_encode([
        "status" => "success",
    ]);
}

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

$path = str_replace(dirname($script_name), '', $request_uri);
$segments = explode('/', trim($path, '/'));
if (isset($segments[1])) {
    $city = $segments[1];
}


function processRequest($pdo, $segments) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($segments[1] == "suggest") {
                echo suggest($pdo, $segments[2]);
            } else {
                echo getPlace($pdo, $segments[1]);
            }
            break;
        case 'POST':
            echo post( $pdo );
            break;
        case 'PUT':
            echo put( $pdo );
            break;
        case 'DELETE':
            echo remove( $pdo );
            break;  
        default:
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid Request",
            ]);
            break;
    }
}


if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    echo processRequest($pdo, $segments);
}

?>