<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> YWEATHER </title>
    <link rel="stylesheet" href="result.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

    <script src="result.js"></script>

</head>

<body>

    <nav>
        <h1 class="league-spartan-600 grey">YWEATHER</h1>
        <div>
            <input type="search" name="" id="getCity" autocomplete="off" placeholder="Search for a city...">
            <div id="suggestions" style="border: 1px solid #ccc; display: none;"></div>
            <button type="submit" id="buttonCity"> weatherrrr </button>

        </div>
        

        <h3 class="league-spartan-600 grey">About</h3>
    </nav>

    <div class="landing">

        <div class="inv"></div>

        <div class="container league-spartan-400">
            <div class="sentence">
                <p class="grey loadAnimationTop invisible">Rainy day in </p>
                <p class="city loadAnimationTop2 invisible">Montpellier</p>
            </div>
            <p class="temp league-spartan-600 grey fadeIn invisible">11°</p>
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
                    .then(data => {
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

        <img class="clouds loadAnimationBottom" src="assets/Clouds.png" alt="">

        <div class="cointainer-hours">

            <div class="weather-carousel">
                <div class="carousel-container">
                    <button class="carousel-arrow carousel-arrow-left">&#10094;</button>
                    <div class="carousel-items">
                        <!-- Heures et données météo -->
                        <div class="weather-item">
                            <div class="weather-time">06:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">15°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">09:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">18°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">12:00</div>
                            <img class="icon" src="assets/soleil.png" alt="">
                            <div class="weather-temp">23°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">15:00</div>
                            <img class="icon" src="assets/soleil.png" alt="">
                            <div class="weather-temp">25°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">18:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">22°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">21:00</div>
                            <img class="icon" src="assets/soleil.png" alt="">
                            <div class="weather-temp">18°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">00:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">16°</div>
                        </div>
                        <!-- Heures supplémentaires -->
                        <div class="weather-item">
                            <div class="weather-time">03:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">14°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">06:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">15°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">09:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">19°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">12:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">22°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time">15:00</div>
                            <img class="icon" src="assets/nuageux.png" alt="">
                            <div class="weather-temp">20°</div>
                        </div>
                        <div class="weather-item">
                            <div class="weather-time"></div>
                            <div class="weather-icon"></div>
                            <div class="weather-temp"></div>
                        </div>
                    </div>
                    <button class="carousel-arrow carousel-arrow-right">&#10095;</button>
                </div>
            </div>

        </div>
        
    </div>

    
    <div class="main">

        <div class="bento-container">
            <!-- Carte principale météo actuelle -->
            <div class="bento-card main-weather">
                <div class="main-weather-top">
                    <div class="glass">
                        <div class="main-temp">24°</div>
                        <div class="main-condition">Ensoleillé</div>
                    </div>
                    <img class="main-icon soleil" src="assets/soleil.png" alt="">
                </div>
                <div class="main-weather-bottom">
                    <div>
                        <div class="main-location">Paris</div>
                        <div class="main-time">Mercredi, 15:30</div>
                    </div>
                    <div>
                        <div class="info-title white">Ressenti</div>
                        <div class="info-value white">26°</div>
                    </div>
                </div>
            </div>
            
            <!-- Carte vent -->
            <div class="bento-card wind-card">
                <div class="info-icon">💨</div>
                <div>
                    <div class="info-title">Vent</div>
                    <div class="info-value">12 km/h</div>
                    <div class="info-desc">Nord-Est</div>
                </div>
            </div>
            
            <!-- Carte humidité -->
            <div class="bento-card humidity-card">
                <div class="info-icon">💧</div>
                <div>
                    <div class="info-title">Humidité</div>
                    <div class="info-value">45%</div>
                    <div class="info-desc">Normale</div>
                </div>
            </div>
            
            <!-- Prévision horaire -->
            <div class="bento-card hourly-forecast">
                <div class="hour-card">
                    <div class="hour-time">15:00</div>
                    <img class="icon" src="assets/soleil.png" alt="">
                    <div class="hour-temp">24°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">16:00</div>
                    <img class="icon" src="assets/soleil.png" alt="">
                    <div class="hour-temp">25°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">17:00</div>
                    <img class="icon" src="assets/nuageux.png" alt="">
                    <div class="hour-temp">24°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">18:00</div>
                    <img class="icon" src="assets/nuageux.png" alt="">
                    <div class="hour-temp">22°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">19:00</div>
                    <img class="icon" src="assets/nuageux.png" alt="">
                    <div class="hour-temp">21°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">20:00</div>
                    <div class="hour-icon">🌙</div>
                    <div class="hour-temp">20°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">21:00</div>
                    <div class="hour-icon">🌙</div>
                    <div class="hour-temp">19°</div>
                </div>
                <div class="hour-card">
                    <div class="hour-time">22:00</div>
                    <div class="hour-icon">🌙</div>
                    <div class="hour-temp">18°</div>
                </div>
            </div>
            
            <!-- Carte indice UV
            <div class="bento-card uv-card">
                <div class="info-icon">☀️</div>
                <div>
                    <div class="info-title">Indice UV</div>
                    <div class="info-value">6</div>
                    <div class="info-desc">Élevé</div>
                </div>
            </div>
            
            Carte pression
            <div class="bento-card pressure-card">
                <div class="info-icon">📊</div>
                <div>
                    <div class="info-title">Pression</div>
                    <div class="info-value">1016 hPa</div>
                    <div class="info-desc">Stable</div>
                </div>
            </div>

            -->
            
            <!-- Prévision quotidienne -->
            <div class="bento-card daily-forecast">

                <div class="background-card">


                    <div class="day-card">
                        <div class="day-name">Mer</div>
                        <div class="day-icon">☀️</div>
                        <div class="day-temp">
                            <span class="day-high">25°</span>
                            <span class="day-low">16°</span>
                        </div>
                    </div>
                    <div class="day-card">
                        <div class="day-name">Jeu</div>
                        <div class="day-icon">⛅</div>
                        <div class="day-temp">
                            <span class="day-high">24°</span>
                            <span class="day-low">15°</span>
                        </div>
                    </div>
                    <div class="day-card">
                        <div class="day-name">Ven</div>
                        <div class="day-icon">🌦️</div>
                        <div class="day-temp">
                            <span class="day-high">22°</span>
                            <span class="day-low">14°</span>
                        </div>
                    </div>
                    <div class="day-card">
                        <div class="day-name">Sam</div>
                        <div class="day-icon">🌧️</div>
                        <div class="day-temp">
                            <span class="day-high">19°</span>
                            <span class="day-low">13°</span>
                        </div>
                    </div>
                    <div class="day-card">
                        <div class="day-name">Dim</div>
                        <div class="day-icon">🌤️</div>
                        <div class="day-temp">
                            <span class="day-high">21°</span>
                            <span class="day-low">12°</span>
                        </div>
                    </div>


                </div>

                
            </div>
        </div>

    </div>


    

</body>
</html>
