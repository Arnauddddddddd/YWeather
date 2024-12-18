<?php
require_once "db.php";

header("Content-Type: application/json");



function get( $pdo, $id ) {
    if ( empty( $id ) ) {
        $stmt = $pdo->prepare("SELECT * FROM Place");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Place WHERE place_id = :place_id");
        $stmt->execute( [ 'place_id' => $id ] );
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    return json_encode([
        "status" => "success",
        "value" => $users,
    ]);
}

function post( $pdo ) {
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

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo get( $pdo, isset($_GET['id']) );
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

?>