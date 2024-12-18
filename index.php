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

    <script>
        document.getElementById('getJoke').addEventListener('click', function() {
            fetch('http://localhost/api.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('joke').textContent = JSON.stringify(data.value);
                })
                .catch(error => {
                    document.getElementById('joke').textContent = 'Failed to fetch a joke. Please try again later.';
                    console.error('Error fetching the joke:', error);
                });
        });
    </script>
</body>
</html>
