<?php
// Include the database functions
require_once __DIR__ . '..\..\helpers\database.php';

// Fetch game statistics
$data = fetch_game_stats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert | Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/stats_view.css">
</head>
<body>
    <h1>Game Statistics</h1>

    <!-- Pie Charts -->
    <div class="chart-container">
        <?php foreach ($data as $descriptor => $outcomes): ?>
            <div class="chart-box">
                <h2><?php echo htmlspecialchars($descriptor); ?></h2>
                <canvas id="chart-<?php echo htmlspecialchars($descriptor); ?>"></canvas>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        <?php foreach ($data as $descriptor => $outcomes): ?>
        new Chart(document.getElementById('chart-<?php echo $descriptor; ?>'), {
            type: 'pie',
            data: {
                labels: ['Wins', 'Draws', 'Losses'],
                datasets: [{
                    data: [
                        <?php echo $outcomes['win']; ?>,
                        <?php echo $outcomes['draw']; ?>,
                        <?php echo $outcomes['lose']; ?>
                    ],
                    backgroundColor: ['#4caf50', '#fbc02d', '#e53935']
                }]
            }
        });
        <?php endforeach; ?>
    </script>

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
    <script src="../js/panels.js"></script>
</body>
</html>
