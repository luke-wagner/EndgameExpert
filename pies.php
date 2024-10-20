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
                var start_date = <?php echo $start_date; ?>;
                var end_date = <?php echo $end_date; ?>;

                fetch('run_script_1.php?username=' + ccom_username + '&start-date=' + start_date + '&end-date=' + end_date)
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

            .details-panel {
                max-width: 1200px;
                margin: 20px auto;
                padding: 0 40px;
            }

            details {
                margin-top: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 10px;
            }

            details summary {
                font-weight: bold;
                cursor: pointer;
            }

            .game-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                padding-top: 10px;
            }

            .game-embed {
                text-align: center;
                width:354px;
                height:354px;
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
    with a as (
        select distinct game_link, descriptor from fens
    )
        select a.descriptor, gd.outcome, count(*) as count
        from a
        inner join game_data gd on (gd.game_link = a.game_link)
        where a.descriptor <> ''
        group by a.descriptor, gd.outcome
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
                        maintainAspectRatio: true,
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

    <!-- Collapsible Panels -->
    <div class="details-panel">
        <?php
        $chartIndex = 0;
        foreach ($data as $descriptor => $outcomes) {
            // Add the descriptor as a data attribute to <details>
            echo "<details data-descriptor='$descriptor'>";
            echo "<summary>View Games for $descriptor</summary>";
            echo "<div class='game-grid' id='gameGrid$chartIndex'></div>";
            echo "</details>";
            $chartIndex++;
        }
        ?>
    </div>

    <script>
        function open_link(link){
            console.log(link);
            window.open(link, '_blank').focus();
        }

        // Function to inject iframes into the game grid when details are opened
        document.querySelectorAll('details').forEach((details, index) => {
            details.addEventListener('toggle', function() {
                if (this.open) {
                    let gameGrid = document.getElementById('gameGrid' + index);
                    let descriptor = this.getAttribute('data-descriptor'); // Get the descriptor from the data attribute
                    
                    var loopCounter = 0;

                    fetch('get_games.php?descriptor=' + descriptor)
                        .then(response => response.json())  // Parse JSON from PHP response
                        .then(data => {
                            data.forEach(game => {
                                if (loopCounter < 18){
                                    // Create and insert the iframe
                                    var iframe = document.createElement('iframe');
                                    iframe.src = 'https://mutsuntsai.github.io/fen-tool/gen/?fen=' + game.fen;  // Replace with your iframe source
                                    iframe.style.border = 'none';  // Remove default iframe border
                                    iframe.style.width = '354px';  // Set width exactly matching container's size
                                    iframe.style.height = '354px';  // Set height exactly matching container's size
                                    iframe.style.position = 'absolute';  // Position iframe absolutely
                                    iframe.style.top = '0px';  // Offset iframe to bring it behind the top border
                                    iframe.style.left = '0px';  // Offset iframe to bring it behind the left border
                                    //iframe.style.right = '-20px';  // Offset iframe to hide it behind the right border
                                    //iframe.style.bottom = '-20px';  // Offset iframe to hide it behind the bottom border

                                    // Create and insert the overlay div
                                    var overlay = document.createElement('div');
                                    overlay.style.position = 'absolute';
                                    overlay.style.top = '0';
                                    overlay.style.left = '0';
                                    overlay.style.width = '100%';
                                    overlay.style.height = '100%';
                                    overlay.style.cursor = 'pointer';  // Indicates that it's clickable

                                    // Attach an event listener to handle clicks
                                    overlay.addEventListener('click', function() { open_link(game.game_link); });

                                    // Append both elements to a container
                                    var container = document.createElement('div');
                                    container.style.position = 'relative';  // Make the container relative to position the overlay
                                    container.style.width = '354px';  // Set width to match the iframe's visible area
                                    container.style.height = '354px';  // Set height to match the iframe's visible area

                                    // Add border and rounded corners
                                    //container.style.border = '6px solid #2EB432';  // Set border color and thickness
                                    container.style.borderRadius = '18px';  // Rounded corners
                                    container.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';  // Subtle shadow for a lifted effect
                                    container.style.overflow = 'hidden';  // Ensure iframe stays within the rounded border
                                    container.style.top = '-4px';
                                    container.style.left = '-4px';
                                    container.style.zIndex = '1';

                                    var container2 = document.createElement('div');
                                    container2.style.width = '345px';  // Set width to match the iframe's visible area
                                    container2.style.height = '345px';  // Set height to match the iframe's visible area
                                    console.log(game.outcome);
                                    if (game.outcome == '1'){
                                        container2.style.border = '6px solid #2EB432';  // Set border color and thickness
                                    } else if (game.outcome == '-1') {
                                        container2.style.border = '6px solid #F54133';  // Set border color and thickness
                                    } else if (game.outcome == '0') {
                                        container2.style.border = '6px solid #808080';  // Set border color and thickness
                                    }
                                    container2.style.borderRadius = '18px';  // Rounded corners
                                    container2.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';  // Subtle shadow for a lifted effect
                                    container2.style.overflow = 'hidden';  // Ensure iframe stays within the rounded border
                                    container2.style.zIndex = '2';

                                    // Append elements to the container
                                    container.appendChild(iframe);
                                    container.appendChild(overlay);

                                    container2.appendChild(container);

                                    gameGrid.appendChild(container2);
                                }

                                loopCounter += 1;

                                //console.log(game.game_link);
                                // Create new elements for each entry
                                //const gameContainer = document.createElement('div');
                                //gameContainer.classList.add('game-item');

                                //const fenText = document.createElement('p');
                                //fenText.textContent = `FEN: ${game.fen}`;

                                //const link = document.createElement('a');
                                //link.href = game.game_link;
                                //link.textContent = "View Game";

                                // Append elements to the container
                                //gameContainer.appendChild(fenText);
                                //gameContainer.appendChild(link);

                                // Append container to the DOM (adjust selector as needed)
                                //document.getElementById('games-list').appendChild(gameContainer);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching games:', error);
                        });
                      
                    /*
                    if (!gameGrid.hasChildNodes()) { // Only load if not already loaded
                        for (let i = 0; i < 6; i++) {
                            let iframe = document.createElement('iframe');
                            var testFen = '2K5/P7/3kN2p/3n3P/8/8/8/8'
                            iframe.src = 'https://mutsuntsai.github.io/fen-tool/gen/?fen=' + testFen;
                            iframe.style.border = 'none';
                            iframe.style.width = '354px';
                            iframe.style.height = '354px';
                            gameGrid.appendChild(iframe);
                        }
                        console.log(`Games loaded for descriptor: ${descriptor}`); // Use descriptor for tracking
                    }
                    */
                }
            });
        });
    </script>

    </body>
    </html>
<?php
}