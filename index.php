
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><!-- TODO : METTRE .php  -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/index.css">

    <script src="https://kit.fontawesome.com/ea8060e81f.js" crossorigin="anonymous"></script>
    <script src="result.js"></script>

    <title> YWEATHER </title>

</head>

<body>

    <div id="follower"></div>

    <nav>
        <div class="nav">
            <h1 class="league-spartan-600 grey">YWEATHER</h1>
            
            <h3 class="league-spartan-600 grey">About</h3>
        </div>
    </nav>

    <div class="landing" id="landing">

        <div class="div-main-content">
            <div class="div">
                <h1 class="main-text"> Wanna look closer at our </br> beautiful api ! </h1>
            </div>            <div class="div">
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" name="id" id="getCity" placeholder="Search for a city ...">
                        <button type="submit" id="buttonCity"> <img src="assets/images/loupe_blanche.png" alt="" srcset=""> </button>
                    </div>
                    <div id="suggestions" class="suggestions-dropdown"></div>
                </div>
                

            </div>

        </div>
 
    </div>

    <script>
            document.getElementById('buttonCity').addEventListener('click', function() {
                let city = document.getElementById('getCity').value
                const suggestions = document.getElementById('suggestions');
                console.log();
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
                        console.log(`URL being fetched: http://localhost/YWeather/${(city)}`);

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
                .then(res => res.json())                .then(data => {
                    suggestions.innerHTML = '';
                    if (data.status === "success" && data.value.length > 0) {
                        data.value.forEach(city => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.innerHTML = `
                                <i class="fas fa-map-marker-alt suggestion-icon"></i>
                                <span class="suggestion-text">${city.name}</span>
                            `;
                            div.style.cursor = "pointer";
                            div.onclick = () => {
                                input.value = city.name;
                                suggestions.style.display = 'none';
                                // Rediriger vers la page de la ville
                                window.location.href = `/YWeather/city/${encodeURIComponent(city.name)}`;
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

</body>
</html>
