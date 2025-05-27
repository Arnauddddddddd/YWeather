<?php

/**
 * Récupère une prédiction météorologique pour une ville spécifique à une heure donnée.
 * Cette fonction exécute un script Python qui effectue la prédiction.
 *
 * @param string $cityName Le nom de la ville pour laquelle obtenir la prédiction.
 * @param float|null $temperature La température actuelle, si disponible. Par défaut à 20.0 si non fournie.
 * @return array|null Un tableau associatif contenant les données de prédiction en cas de succès, sinon null.
 */
function getPredictionForCity($cityName, $temperature = null) {
    // Utilise la température fournie ou une valeur par défaut.
    $currentTemp = $temperature ?? 20.0;
    // Obtient la date et l'heure actuelles au format requis par le script Python.
    $currentDate = date('Y-m-d');
    $currentHour = date('H');
    
    // Échappe le nom de la ville pour s'assurer qu'il est sûr d'être passé en argument de ligne de commande.
    $safeCityName = escapeshellarg($cityName);
    
    // Construit le chemin relatif vers le script Python de prédiction.
    $pythonPath = dirname(__DIR__) . "/model/prediction/prediction.py";
    
    // Construit la commande shell à exécuter.
    // "python" : commande pour exécuter Python.
    // "$pythonPath" : chemin vers le script Python.
    // "predict" : argument indiquant au script Python de faire une prédiction ponctuelle.
    // "$currentDate", "$currentHour", "$safeCityName", "$currentTemp" : arguments passés au script Python.
    // "2>&1" : redirige la sortie d'erreur standard (stderr) vers la sortie standard (stdout),
    //          ce qui permet de capturer les messages d'erreur du script Python dans $output.
    $command = "python \"$pythonPath\" predict $currentDate $currentHour $safeCityName $currentTemp 2>&1";
    
    // Exécute la commande shell et capture sa sortie.
    $output = shell_exec($command);
    // Décode la sortie JSON du script Python en un tableau associatif PHP.
    $result = json_decode($output, true);
    
    // Vérifie si le décodage a réussi et si le statut de la réponse est 'success'.
    if ($result && $result['status'] === 'success') {
        // Retourne les données de prédiction.
        return $result['data'];
    }
    
    // En cas d'échec, retourne null.
    return null;
}

/**
 * Récupère une prédiction météorologique hebdomadaire pour une ville spécifique.
 * Cette fonction exécute un script Python qui effectue la prédiction hebdomadaire.
 *
 * @param string $cityName Le nom de la ville pour laquelle obtenir la prédiction hebdomadaire.
 * @param float|null $temperature La température actuelle, si disponible. Par défaut à 20.0 si non fournie.
 * @return array|null Un tableau associatif contenant les données de prédiction hebdomadaire en cas de succès, sinon null.
 */
function getWeeklyPredictionForCity($cityName, $temperature = null) {
    // Utilise la température fournie ou une valeur par défaut.
    $currentTemp = $temperature ?? 20.0;
    // Obtient la date et l'heure actuelles au format requis par le script Python.
    $currentDate = date('Y-m-d');
    $currentHour = date('H');
    
    // Échappe le nom de la ville pour s'assurer qu'il est sûr d'être passé en argument de ligne de commande.
    $safeCityName = escapeshellarg($cityName);
    
    // Construit le chemin relatif vers le script Python de prédiction.
    $pythonPath = dirname(__DIR__) . "/model/prediction/prediction.py";
    
    // Construit la commande shell à exécuter.
    // "python" : commande pour exécuter Python.
    // "$pythonPath" : chemin vers le script Python.
    // "weekly" : argument indiquant au script Python de faire une prédiction hebdomadaire.
    // "$currentDate", "$currentHour", "$safeCityName", "$currentTemp" : arguments passés au script Python.
    // "2>&1" : redirige la sortie d'erreur standard (stderr) vers la sortie standard (stdout).
    $command = "python \"$pythonPath\" weekly $currentDate $currentHour $safeCityName $currentTemp 2>&1";
    
    // Exécute la commande shell et capture sa sortie.
    $output = shell_exec($command);
    // Décode la sortie JSON du script Python en un tableau associatif PHP.
    $result = json_decode($output, true);
    
    // Vérifie si le décodage a réussi et si le statut de la réponse est 'success'.
    if ($result && $result['status'] === 'success') {
        // Retourne les données de prédiction hebdomadaire.
        return $result['data'];
    }
    
    // En cas d'échec, retourne null.
    return null;
}

?>