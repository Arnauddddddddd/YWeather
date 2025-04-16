<?php


# Pour l'instant ça ne marche pas, c'est parce que je n'ai pas encore ajouté mon user et
# mot de passe de l'API 

# l'api que j'ai trouvée fonctionne avec longitude et lattitude, on compte prendre une API ou librairie pour transformer un lien en longitude et lattitude




// $url = "https://api.meteomatics.com/2025-01-22T00:00:00Z/t_2m:C/43.6,3.87/json";

// $churl = curl_init();
// curl_setopt($churl, CURLOPT_URL, $url);
// curl_setopt($churl, CURLOPT_RETURNTRANSFER, true); 
// $response = curl_exec($churl);

// function httpPostJson($url, $data) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $result = curl_exec($ch);
//     curl_close($ch);
//     return $result;
// }


require_once "../db/db.php";


$command = escapeshellcmd('python3 api.py');
$output = shell_exec($command);

$sql_queries = $output;

// Divisez les requêtes en lignes (si nécessaire)
$queries = explode("\n", $sql_queries);

foreach ($queries as $query) {
    if (!empty($query)) {
        // Exécuter la requête SQL avec PDO
        $pdo->exec($query);
        echo "Query executed successfully: $query\n";
    }
}






?>