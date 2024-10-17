<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            font-weight: 700;
            color: #444;
            margin-top: 40px;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: top;
            height: 100vh;
        }

        .form-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin-top:20px;
        }

        .form-box label {
            font-size: 1em;
            color: #666;
            margin-bottom: 10px;
            display: block;
        }

        .form-box input[type="text"],
        .form-box input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-box button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            font-size: 1em;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-box button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .form-box {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<h1>Endgame Expert</h1>

<div class="form-container">
    <div class="form-box">
        <form action="pies.php" method="GET">
            <label for="username">Chess.com username:</label>
            <input type="text" id="username" name="username" required>

            <label for="start-date">Start date:</label>
            <input type="date" id="start-date" name="start-date" required>

            <label for="end-date">End date:</label>
            <input type="date" id="end-date" name="end-date" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

</body>
</html>
