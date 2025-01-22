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
        <p id="joke">Click the button </p>
        <button id="getJoke"> weatherrrr </button>
    </div>


    <div class="container">
        <h1> YWEATHER </h1>
        
        <input id="getCity">Click the button </p>
        <button type="submit" id="buttonCity"> weatherrrr </button>
    </div>

    <script>
        document.getElementById('getJoke').addEventListener('click', function() {
            fetch('http://localhost/YWeather/src/CRUD/api.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('joke').textContent = JSON.stringify(data.value);
                })
                .catch(error => {
                    document.getElementById('joke').textContent = 'Failed to fetch a joke. Please try again later.';
                    console.error('Error fetching the joke:', error);
                });
        });


        document.getElementById('buttonCity').addEventListener('click', function() {
            let test = document.getElementById('getCity').value
            console.log(test)
            fetch(`http://localhost/YWeather/src/CRUD/api.php?city=${encodeURIComponent(test)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('buttonCity').textContent = JSON.stringify(data.value);
                })
                
                .catch(error => {
                    document.getElementById('getCity').textContent = 'Failed to fetch a joke. Please try again later.';
                    console.error('Error fetching the joke:', error);
                });
        });
    </script>

    <?php
        $jsoncontent = "";
        if ( isset($_GET["route"]) ) {
            $request_uri = $_GET["route"];
            $segments = explode('/', trim($request_uri, characters: '/'));

            if ( $segments[0] == (int)$segments[0] ) {
                $jsoncontent = file_get_contents("http://localhost/YWeather/src/CRUD/api.php?id=" . $segments[0]);
            } else {
                $jsoncontent = file_get_contents("http://localhost/YWeather/src/CRUD/api.php?place=" . $segments[0]);
            }
        } else {
            $jsoncontent = file_get_contents("http://localhost/YWeather/src/CRUD/api.php");
        }
    ?>

    <p><?= $jsoncontent ?></p>


</body>
</html>
