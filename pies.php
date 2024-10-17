<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert | Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        h2 {
            font-size: 1.2em;
            font-weight: 500;
            color: #666;
            margin-bottom: 10px;
        }

        .chart-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .chart-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .chart-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .chart-box canvas {
            width: 100% !important;
            height: auto !important;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2em;
            }

            .chart-box {
                padding: 15px;
            }

            .chart-box canvas {
                width: 100% !important;
                height: 250px !important;
            }
        }
    </style>
</head>
<body>

<?php
// Read in login credentials from .env file
$env = parse_ini_file('.env');
$db_username = $env["DB_USERNAME"];
$db_pwd = $env["DB_PWD"];

// 1. Connect to MySQL database
$servername = "localhost";
$dbname = "chessapp";

$conn = new mysqli($servername, $db_username, $db_pwd, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Run your query to fetch game outcomes
$sql = "
    SELECT f.descriptor, gd.outcome, COUNT(*) AS count
    FROM fens f
    INNER JOIN game_data gd ON (gd.game_link = f.game_link)
    WHERE f.descriptor <> ''
    GROUP BY f.descriptor, gd.outcome
";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    // 3. Organize data into descriptor groups
    while ($row = $result->fetch_assoc()) {
        $descriptor = $row['descriptor'];
        $outcome = $row['outcome'];
        $count = $row['count'];

        if (!isset($data[$descriptor])) {
            $data[$descriptor] = ['win' => 0, 'draw' => 0, 'lose' => 0];
        }
        
        if ($outcome == 1) {
            $data[$descriptor]['win'] = $count;
        } elseif ($outcome == 0) {
            $data[$descriptor]['draw'] = $count;
        } elseif ($outcome == -1) {
            $data[$descriptor]['lose'] = $count;
        }
    }
}

// Close the connection
$conn->close();
?>

<h1>Game Outcome Stats</h1>

<div class="chart-container">
<?php
// 4. Generate the HTML for each pie chart
$chartIndex = 0; // To ensure unique chart IDs
foreach ($data as $descriptor => $outcomes) {
    $win = $outcomes['win'];
    $draw = $outcomes['draw'];
    $lose = $outcomes['lose'];
    $chartId = "chart" . $chartIndex;

    echo "<div class='chart-box'>";
    echo "<h2>$descriptor</h2>";
    echo "<canvas id='$chartId'></canvas>";
    echo "<script>
        var ctx$chartIndex = document.getElementById('$chartId').getContext('2d');
        new Chart(ctx$chartIndex, {
            type: 'pie',
            data: {
                labels: ['Wins ($win)', 'Draws ($draw)', 'Losses ($lose)'],
                datasets: [{
                    data: [$win, $draw, $lose],
                    backgroundColor: ['#4caf50', '#ffeb3b', '#f44336'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Maintain aspect ratio for pie charts
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    </script>";
    echo "</div>";
    
    $chartIndex++; // Increment chart index to avoid ID duplication
}
?>
</div>

</body>
</html>
