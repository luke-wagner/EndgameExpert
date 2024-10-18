<?php
if (isset($_GET['username']) && isset($_GET['start-date']) && isset($_GET['end-date'])) {
    // Get form data
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Loading...</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
                background-color: #f5f5f5;
                color: #333;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .loading-container {
                text-align: center;
                background-color: #fff;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .loading-container h1 {
                font-size: 2.5em;
                font-weight: 700;
                color: #444;
                margin-bottom: 20px;
            }

            .loading-animation {
                border: 6px solid #f3f3f3;
                border-radius: 50%;
                border-top: 6px solid #4caf50;
                width: 60px;
                height: 60px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .loading-message {
                font-size: 1.2em;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="loading-container">
            <h1>Loading</h1>
            <div class="loading-animation"></div>
            <p class="loading-message">Please wait while we process your data...</p>
            <p id="load-message-l2" class="loading-message">Downloading files...</p>
            <p id="shell-data" class="loading-message"></p>
        </div>

        <script>
            // Send an AJAX request to trigger the PHP script
            window.onload = function() {
                var ccom_username = <?php echo $ccom_username; ?>;

                fetch('run_script_1.php?username=' + ccom_username)
                    .then(response => response.text())
                    .then(data => {
                        // When the PHP script finishes, hide the loading message and show the content
                        //document.getElementById('loading').style.display = 'none';
                        //document.getElementById('content').style.display = 'block';
                        document.getElementById('load-message-l2').innerHTML = "Clearing DB...";
                        document.getElementById('shell-data').innerHTML = data;
                        //window.location.href = window.location.pathname; // Strip away the extra params in URL

                    })
                    .catch(error => console.error('Error:', error));
                    
                fetch('run_script_2.php')
                    .then(response => response.text())
                    .then(data => {
                        // When the PHP script finishes, hide the loading message and show the content
                        //document.getElementById('loading').style.display = 'none';
                        //document.getElementById('content').style.display = 'block';
                        document.getElementById('load-message-l2').innerHTML = "Evaluating positions...";
                        document.getElementById('shell-data').innerHTML = data;
                        //window.location.href = window.location.pathname; // Strip away the extra params in URL

                    })
                    .catch(error => console.error('Error:', error));

                fetch('run_script_3.php?username=' + ccom_username)
                    .then(response => response.text())
                    .then(data => {
                        // When the PHP script finishes, hide the loading message and show the content
                        //document.getElementById('loading').style.display = 'none';
                        //document.getElementById('content').style.display = 'block';
                        window.location.href = window.location.pathname; // Strip away the extra params in URL
                    })
                    .catch(error => console.error('Error:', error));

            };
        </script>
    </body>
    </html>
<?php
} else {
?>
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
<?php
}