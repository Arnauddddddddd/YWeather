<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> YWEATHER </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

    <script src="result.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/city.css">

</head>

<body>    <?php 
        require_once dirname(__DIR__) . '/API/crud.php';
        require_once dirname(__DIR__) . '/API/crudPlace.php';
        require_once dirname(__DIR__) . '/API/predictions.php';
        require_once dirname(__DIR__) . '../src/db/db.php';

   
        $request_uri = $_SERVER['REQUEST_URI'];
        $script_name = $_SERVER['SCRIPT_NAME'];        $path = str_replace(dirname($script_name), '', $request_uri);
        $segments = explode('/', trim($path, '/'));

        $cityName = isset($segments[2]) ? $segments[2] : ($_GET['city'] ?? 'Paris');
        $cityName = urldecode($cityName);

        $city = getPlace($pdo, $cityName);
        $cityArray = json_decode($city, true)["value"][0] ?? null;
        $cityId = (int) $cityArray["place_id"] ?? null;        $weather = getLastWeathersByPlace($pdo, 1,$cityId)[0]; // Example usage
        
        // Obtenir la dernière date de données disponible pour cette ville
        $latestDataDate = getLatestDataDateForPlace($pdo, $cityId);
        $predictionStartDate = null;
        
        if ($latestDataDate) {
            // Calculer le jour suivant la dernière date de données disponible
            $latestDateTime = new DateTime($latestDataDate);
            $predictionStartDate = clone $latestDateTime;
            $predictionStartDate->add(new DateInterval('P1D')); // Ajouter 1 jour
        } else {
            // Fallback si pas de données : utiliser demain
            $predictionStartDate = new DateTime('tomorrow');
        }
        
        // Obtenir les prédictions météorologiques via Python
        $currentTemperature = (float) $weather["temperature"];
        $currentPrediction = getPredictionForCity($cityName, $currentTemperature);
        $weeklyPrediction = getWeeklyPredictionForCity($cityName, $currentTemperature);
    ?>

    <div id="follower"></div>

    <nav>
        <div class="nav">
            <h1 class="league-spartan-600 white">YWEATHER</h1>            <div class="search-bar">
                <div class="search-bar-container">
                    <input type="search" name="" id="getCity" class="getCity" autocomplete="off" placeholder="Search for a city...">
                    <button type="button" id="clearCity" class="clear-btn" style="display: none;"> <i class="fas fa-times"></i> </button>
                    <button type="submit" id="buttonCity" class="search-btn"> <i class="fas fa-search"></i> </button>
                    <div id="suggestions" class="suggestions-dropdown"></div>
                </div>
            </div>
            <h3 class="league-spartan-600 white">About</h3>
        </div>
    </nav>


    <div class="landing" id="landing">        <div class="inv"></div>


        <div class="container league-spartan-400">
            <div class="sentence">
                <p class="white loadAnimationTop invisible">What a good day in </p>
                <p class="city loadAnimationTop2 invisible"> <?php echo $cityName ?> </p>
            </div>
            <p class="temp league-spartan-600 white fadeIn invisible">
                <?php 
                if ($currentPrediction && isset($currentPrediction['temperature_2m'])) {
                    echo (int)$currentPrediction['temperature_2m'];
                } else {
                    echo (int)$weather["temperature"];
                }
                ?> °
            </p>
        </div>

        <script>
            document.getElementById('buttonCity').addEventListener('click', function() {
                let city = document.getElementById('getCity').value
                const suggestions = document.getElementById('suggestions');
                if (!city) {
                    document.getElementById('City').textContent = 'Failed to fetch a city.';
                    return;
                }
                fetch(`http://localhost/YWeather/${encodeURIComponent(city)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (Array.isArray(data.value) && data.value.length === 0) {
                            document.getElementById('City').textContent = 'Failed to fetch a city.';
                            return;
                        }
                        let cityName = data.value[0].name;
                        console.log(cityName);
                        window.location.href = `/YWeather/city/${encodeURIComponent(cityName)}`;
                    })
                    
                    .catch(error => {
                        document.getElementById('getCity').textContent = 'Failed to fetch a city.';
                        console.error('Error fetching the joke:', error);
                    });
            });
    
            
        </script>
        <script>
            const input = document.getElementById('getCity');
            const suggestions = document.getElementById('suggestions');
    
            input.addEventListener('input', () => {
                const query = input.value.trim();
    
                if (query.length < 2) {
                    suggestions.style.display = 'none';
                    return;
                }
                let url = `http://localhost/YWeather/suggest/${encodeURIComponent(query)}`
                console.log(url);  
                fetch(url)
                    .then(res => res.json())
                    .then data => {
                        suggestions.innerHTML = '';
                        if (data.status === "success" && data.value.length > 0) {
                            data.value.forEach(city => {
                                const div = document.createElement('div');
                                div.textContent = city.name;
                                div.style.cursor = "pointer";
                                div.onclick = () => {
                                    input.value = city.name;
                                    suggestions.style.display = 'none';
                                };
                                suggestions.appendChild(div);
                            });
                            suggestions.style.display = 'block';
                        } else {
                            suggestions.style.display = 'none';
                        }
                    })
                    .catch(err => {
                        console.error("Erreur autocomplétion:", err);
                        suggestions.style.display = 'none';
                    });
            });
        </script>


        <img class="clouds loadAnimationBottom" src="assets/Clouds4.png" alt="">




        <div class="bento-container">            <!-- Carte principale météo actuelle -->
            <div class="bento-card main-weather">
                <div class="main-weather-top">
                    <div class="glass">
                        <div class="main-temp">
                            <?php 
                            if ($currentPrediction && isset($currentPrediction['temperature_2m'])) {
                                echo (int)$currentPrediction['temperature_2m'];
                            } else {
                                echo (int)$weather["temperature"];
                            }
                            ?>°
                        </div>
                        <div class="main-condition">
                            <?php 
                            if ($currentPrediction) {
                                // Déterminer la condition basée sur les données prédites
                                $temp = $currentPrediction['temperature_2m'];
                                $precipitation = $currentPrediction['total_precipitation'];
                                $humidity = $currentPrediction['humidity_2m'];
                                
                                if ($precipitation > 2) {
                                    echo "Pluvieux";
                                } elseif ($precipitation > 0.1) {
                                    echo "Légèrement pluvieux";
                                } elseif ($humidity > 80) {
                                    echo "Nuageux";
                                } elseif ($temp > 25) {
                                    echo "Ensoleillé";
                                } else {
                                    echo "Dégagé";
                                }
                            } else {
                                echo $weather["state"];
                            }
                            ?>
                        </div>
                    </div>
                    <img class="main-icon soleil" src="assets/images/soleil.png" alt="">
                </div>                <div class="main-weather-bottom">
                    <div>
                        <div class="main-location"><?php echo $cityName ?></div>
                        <div class="main-time">
                            <?php 
                            // Afficher la date de prédiction au lieu de la date actuelle
                            if ($predictionStartDate) {
                                echo 'Prédiction pour le ' . $predictionStartDate->format('d/m/Y');
                            } else {
                                echo date('l, H:i', time());
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <div class="info-title white">Ressenti</div>
                        <div class="info-value white">
                            <?php 
                            // Calculer température ressentie approximative
                            $actualTemp = $currentPrediction ? $currentPrediction['temperature_2m'] : $weather["temperature"];
                            $humidity = $currentPrediction ? $currentPrediction['humidity_2m'] : $weather["humidity"];
                            $wind = $currentPrediction ? $currentPrediction['wind_speed_10m'] : $weather["wind"];
                            
                            $feltTemp = $actualTemp;
                            if ($humidity > 70) $feltTemp += 2; // chaud si humide
                            if ($wind > 15) $feltTemp -= 3; // froid si venteux
                            
                            echo (int)$feltTemp;
                            ?>°
                        </div>
                    </div>
                </div>
            </div>
              <!-- Carte vent -->
            <div class="bento-card wind-card">
                <div class="info-icon">💨</div>
                <div>
                    <div class="info-title">Vent</div>
                    <div class="info-value">
                        <?php 
                        if ($currentPrediction && isset($currentPrediction['wind_speed_10m'])) {
                            echo (int)$currentPrediction['wind_speed_10m'] . ' km/h';
                        } else {
                            echo (int)$weather["wind"] . ' km/h';
                        }
                        ?>
                    </div>
                    <div class="info-desc">Nord-Est</div>
                </div>
            </div>
            
            <!-- Carte humidité -->
            <div class="bento-card humidity-card">
                <div class="info-icon">💧</div>
                <div>
                    <div class="info-title">Humidité</div>
                    <div class="info-value">
                        <?php 
                        if ($currentPrediction && isset($currentPrediction['humidity_2m'])) {
                            echo (int)$currentPrediction['humidity_2m'] . '%';
                        } else {
                            echo (int)$weather["humidity"] . '%';
                        }
                        ?>
                    </div>
                    <div class="info-desc">
                        <?php 
                        $humidity = $currentPrediction ? $currentPrediction['humidity_2m'] : $weather["humidity"];
                        if ($humidity < 40) echo "Faible";
                        elseif ($humidity > 70) echo "Élevée";
                        else echo "Normale";
                        ?>
                    </div>
                </div>
            </div>            <!-- Carte précipitations -->
            <div class="bento-card precipitation-card">
                <div class="precipitation-header">
                    <div class="info-icon">🌧️</div>
                    <div class="precipitation-info">
                        <div class="info-title">Précipitations</div>
                        <div class="info-value">
                            <?php 
                            if ($currentPrediction && isset($currentPrediction['total_precipitation'])) {
                                echo number_format($currentPrediction['total_precipitation'], 1) . ' mm';
                            } else {
                                echo '0.0 mm';
                            }
                            ?>
                        </div>
                        <div class="info-desc">
                            <?php 
                            $precipitation = $currentPrediction ? $currentPrediction['total_precipitation'] : 0;
                            if ($precipitation == 0) echo "Aucune";
                            elseif ($precipitation < 2.5) echo "Faible";
                            elseif ($precipitation < 10) echo "Modérée";
                            else echo "Forte";
                            ?>
                        </div>
                    </div>
                </div>                <div class="precipitation-chart">
                    <div class="chart-title">
                        <?php 
                        if ($predictionStartDate) {
                            echo 'Risque de précipitations le ' . $predictionStartDate->format('d/m');
                        } else {
                            echo 'Risque de précipitations aujourd\'hui';
                        }
                        ?>
                    </div>
                    <div class="precipitation-summary">                        <div class="summary-text">
                            <?php 
                            $precipitation = $currentPrediction ? $currentPrediction['total_precipitation'] : 0;
                            $dateText = $predictionStartDate ? ('le ' . $predictionStartDate->format('d/m')) : 'aujourd\'hui';
                            
                            if ($precipitation == 0) {
                                echo "Aucune précipitation attendue pour " . $dateText . ".";
                            } elseif ($precipitation < 2.5) {
                                echo "Faibles précipitations possibles " . $dateText . ".";
                            } elseif ($precipitation < 10) {
                                echo "Précipitations modérées prévues pour " . $dateText . ".";
                            } else {
                                echo "Fortes précipitations attendues " . $dateText . ".";
                            }
                            ?>
                        </div>
                        <div class="summary-advice">
                            <?php 
                            if ($precipitation > 2) {
                                echo "N'oubliez pas votre parapluie ! ☂️";
                            } else {
                                echo "Pas besoin de parapluie aujourd'hui ! ☀️";
                            }
                            ?>
                        </div>
                    </div>
                </div></div><!-- Prévision horaire -->
            <div class="bento-card hourly-forecast" style="display: none;">
                <?php 
                // Générer des prévisions horaires pour toute la journée (0h-23h)
                $baseTemp = $currentPrediction ? $currentPrediction['temperature_2m'] : $weather["temperature"];
                
                for ($hour = 0; $hour < 24; $hour++) {
                    $hourStr = sprintf('%02d:00', $hour);
                    
                    // Variation réaliste de température au cours de la journée
                    $tempVariation = 0;
                    
                    // Courbe de température réaliste
                    if ($hour >= 0 && $hour <= 5) $tempVariation = -4; // Nuit froide
                    elseif ($hour >= 6 && $hour <= 8) $tempVariation = -2; // Matin frais
                    elseif ($hour >= 9 && $hour <= 11) $tempVariation = 1; // Matinée qui se réchauffe
                    elseif ($hour >= 12 && $hour <= 14) $tempVariation = 5; // Midi chaud
                    elseif ($hour >= 15 && $hour <= 17) $tempVariation = 4; // Après-midi chaud
                    elseif ($hour >= 18 && $hour <= 20) $tempVariation = 2; // Soirée douce
                    else $tempVariation = -1; // Fin de soirée
                    
                    $hourTemp = (int)($baseTemp + $tempVariation + rand(-1, 1));
                    
                    // Choisir l'icône en fonction de l'heure et des conditions
                    if ($hour >= 6 && $hour <= 18) {
                        if ($currentPrediction && $currentPrediction['total_precipitation'] > 1) {
                            $icon = '<img class="icon" src="../assets/images/nuageux.png" alt="">';
                        } else {
                            $icon = '<img class="icon" src="../assets/images/soleil.png" alt="">';
                        }
                    } else {
                        $icon = '<div class="hour-icon">🌙</div>';
                    }
                ?>
                <div class="hour-card">
                    <div class="hour-time"><?php echo $hourStr ?></div>
                    <?php echo $icon ?>
                    <div class="hour-temp"><?php echo $hourTemp ?>°</div>
                </div>
                <?php } ?>
            </div>              <!-- Prévision quotidienne -->
            <div class="bento-card daily-forecast">            <?php            if ($weeklyPrediction && is_array($weeklyPrediction) && count($weeklyPrediction) > 0) {
                    // Utiliser la date de prédiction calculée basée sur les données disponibles
                    $dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    $startDate = $predictionStartDate ? clone $predictionStartDate : new DateTime('tomorrow');
                    
                    for ($i = 0; $i < 7; $i++) { // 7 jours à partir de la date de prédiction
                        $currentDate = clone $startDate;
                        $currentDate->add(new DateInterval('P' . $i . 'D'));
                        
                        $dayName = $dayNames[$currentDate->format('N') - 1];
                        $formattedDate = $currentDate->format('d/m');
                        
                        // Utiliser les données de prédiction si disponibles, sinon générer
                        $dayPred = isset($weeklyPrediction[$i + 1]) ? $weeklyPrediction[$i + 1] : null; // +1 car on commence au jour suivant
                        
                        if ($dayPred) {
                            // Utiliser les vraies données
                            $precipitation = $dayPred['total_precipitation'];
                            $temperature = $dayPred['temperature_2m'];
                            $humidity = $dayPred['humidity_2m'];
                        } else {
                            // Générer des données approximatives
                            $precipitation = rand(0, 3);
                            $temperature = rand(15, 25);
                            $humidity = rand(60, 85);
                        }
                        
                        // Déterminer l'icône basée sur les conditions météo
                        $icon = '☀️'; // Par défaut
                        if ($precipitation > 5) {
                            $icon = '🌧️';
                        } elseif ($precipitation > 0.5) {
                            $icon = '🌦️';
                        } elseif ($humidity > 80) {
                            $icon = '⛅';
                        } elseif ($temperature > 25) {
                            $icon = '☀️';
                        } else {
                            $icon = '🌤️';
                        }
                        
                        // Calculer température min/max (approximation)
                        $tempHigh = (int)$temperature + 3;
                        $tempLow = (int)$temperature - 5;
                ?>
                <div class="day-card">
                    <div class="day-name"><?php echo $dayName ?></div>
                    <div class="day-date" style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 4px;"><?php echo $formattedDate ?></div>
                    <div class="day-icon"><?php echo $icon ?></div>
                    <div class="day-temp">
                        <span class="day-high"><?php echo $tempHigh ?>°</span>
                        <span class="white">|</span>
                        <span class="day-low"><?php echo $tempLow ?>°</span>
                    </div>
                </div>
                <?php 
                    }                } else {
                    // Fallback avec données dynamiques pour 7 jours si pas de prédictions
                    $fallbackDays = [];
                    $fallbackStartDate = $predictionStartDate ? clone $predictionStartDate : new DateTime('tomorrow');
                    
                    for ($j = 0; $j < 7; $j++) {
                        $fallbackDate = clone $fallbackStartDate;
                        $fallbackDate->add(new DateInterval('P' . $j . 'D'));
                        
                        $dayName = $dayNames[$fallbackDate->format('N') - 1];
                        $formattedDate = $fallbackDate->format('d/m');
                        
                        // Générer des données météo aléatoires mais réalistes
                        $icons = ['☀️', '⛅', '🌤️', '🌦️', '🌧️'];
                        $icon = $icons[array_rand($icons)];
                        $high = rand(18, 28);
                        $low = rand(10, 18);
                        
                        $fallbackDays[] = [
                            'name' => $dayName,
                            'icon' => $icon,
                            'high' => $high,
                            'low' => $low,
                            'date' => $formattedDate
                        ];
                    }
                    
                    foreach ($fallbackDays as $day) {
                ?>
                <div class="day-card">
                    <div class="day-name"><?php echo $day['name'] ?></div>
                    <div class="day-date" style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 4px;"><?php echo $day['date'] ?></div>
                    <div class="day-icon"><?php echo $day['icon'] ?></div>
                    <div class="day-temp">
                        <span class="day-high"><?php echo $day['high'] ?>°</span>
                        <span class="white">|</span>
                        <span class="day-low"><?php echo $day['low'] ?>°</span>
                    </div>
                </div>
                <?php 
                    }
                } ?>
            </div>
            </div>
        </div>


    </div>


    <!-- Script de debug pour les prédictions -->
    <script>        // Données de prédiction pour debug
        const predictionData = {
            currentPrediction: <?php echo json_encode($currentPrediction, JSON_PRETTY_PRINT); ?>,
            weeklyPrediction: <?php echo json_encode($weeklyPrediction, JSON_PRETTY_PRINT); ?>,
            cityName: "<?php echo $cityName; ?>",
            currentTemperature: <?php echo (float)$weather["temperature"]; ?>,
            latestDataDate: "<?php echo $latestDataDate ?: 'non disponible'; ?>",
            predictionStartDate: "<?php echo $predictionStartDate ? $predictionStartDate->format('d/m/Y') : 'non calculée'; ?>"
        };

        console.log("=== YWEATHER PREDICTION DEBUG ===");
        console.log("Ville:", predictionData.cityName);
        console.log("Température actuelle (DB):", predictionData.currentTemperature + "°C");
        console.log("Dernière date de données:", predictionData.latestDataDate);
        console.log("Date de début des prédictions:", predictionData.predictionStartDate);
        
        if (predictionData.currentPrediction) {
            console.log("✓ Prédiction actuelle disponible:");
            console.log("  - Température prédite:", predictionData.currentPrediction.temperature_2m + "°C");
            console.log("  - Humidité:", predictionData.currentPrediction.humidity_2m + "%");
            console.log("  - Précipitations:", predictionData.currentPrediction.total_precipitation + "mm");
            console.log("  - Vent:", predictionData.currentPrediction.wind_speed_10m + "km/h");
        } else {
            console.log("❌ Prédiction actuelle non disponible");
        }

        if (predictionData.weeklyPrediction && predictionData.weeklyPrediction.length > 0) {
            console.log("✓ Prédiction hebdomadaire disponible:", predictionData.weeklyPrediction.length + " jours");
            predictionData.weeklyPrediction.forEach((day, index) => {
                console.log(`  Jour ${index + 1} (${day.date}): ${day.temperature_2m}°C - ${day.day_name}`);
            });
        } else {
            console.log("❌ Prédiction hebdomadaire non disponible");
        }
        
        console.log("=== FIN DEBUG ===");
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.fog.min.js"></script>
    <script>
    VANTA.FOG({
        el: "#landing",
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        highlightColor: 0x4788ff,
        midtoneColor: 0x7294ff,
        lowlightColor: 0x283ee0,
        baseColor: 0x242dbd,
        blurFactor: 0.90,
        speed: 2.70,
        zoom: 0.40
    })
    </script>

    
    <div class="main">

        

    </div>
</body>
</html>


