<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> YWEATHER </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        #joke {
            margin: 20px 0;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> YWEATHER </h1>
        
        <input type="text" id="getCity" placeholder="Entrez une ville" autocomplete="off" />
        <div id="suggestions" style="border: 1px solid #ccc; display: none;"></div>

        <button type="submit" id="buttonCity"> weatherrrr </button>

        <p id="City"> </p>
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

    

    


</body>
</html>
