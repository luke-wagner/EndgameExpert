<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

if (isset($_GET['username']) && isset($_GET['start-date']) && isset($_GET['end-date'])) {
    // Get form data
    $session_id = escapeshellarg($_GET['session']);
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Fetch game statistics
    $data = fetch_game_stats($ccom_username, $start_date, $end_date);
} else {
    $data = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert | Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/x-icon" href="/img/knight-icon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/stats_view.css">
</head>
<body>
    <h1>Stats for <?= trim($ccom_username, '"\'') ?></h1>
    <div class="timeframe-container">
        <form id="timeframe-form">
            <input type="hidden" name="session" value="">
            <input type="hidden" name="username" value="<?= trim($ccom_username, '"\'') ?>">
            <label>Timeframe:</label>
            <input type="date" name="start-date" value="<?= trim($start_date, '"\'') ?>" required>
            <span>to</span>
            <input type="date" name="end-date" value="<?= trim($end_date, '"\'') ?>" required>
            <button type="submit">Update</button>
        </form>
    </div>
    
    <img class="board-1" src="../img/board-cropped.png">
    <img class="board-2" src="../img/board.png">

    <?php if (!empty($data)): ?>
        <!-- Pie Charts -->
        <div class="chart-container">
            <?php foreach ($data as $descriptor => $outcomes): ?>
                <div class="chart-box">
                    <h2><?php echo htmlspecialchars($descriptor); ?></h2>
                    <canvas id="chart-<?php echo htmlspecialchars($descriptor); ?>" width="300" height="300"></canvas>
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
                echo "<details data-descriptor='$descriptor'>";
                echo "<summary>View Games for $descriptor</summary>";
                echo "<div class='game-grid' id='gameGrid$chartIndex'></div>";
                echo "</details>";
                $chartIndex++;
            }
            ?>
        </div>
        <script src="../js/panels.js"></script>

    <?php else: ?>
        <!-- No Data Message -->
        <div class="no-data">
            <p>No data for this user and time period.</p>
        </div>
    <?php endif; ?>

    <script src="../js/stats_view.js"></script>
</body>
</html>
