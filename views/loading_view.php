<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/loading_view.css">
</head>
<body>
    <div class="loading-container">
        <h1>Loading</h1>
        <div class="loading-animation"></div>
        <p class="loading-message">Please wait while we process your data...</p>
        <p id="status-message" class="loading-message"></p>
        <div id="error-box" class="error-box">
            <p id="error-message" class="error-message"></p>
        </div>
    </div>

    <script>
        <?php
        if (isset($_GET['username']) && isset($_GET['start-date']) && isset($_GET['end-date'])) {
            // Get form data
            $ccom_username = escapeshellarg($_GET['username']);
            $start_date = escapeshellarg($_GET['start-date']);
            $end_date = escapeshellarg($_GET['end-date']);
        }
        ?>

        var ccom_username = <?php echo $ccom_username; ?>;
        var start_date = <?php echo $start_date; ?>;
        var end_date = <?php echo $end_date; ?>;

        fetch('scripts/script01_fetch_games.php?username=' + ccom_username + '&start-date=' + start_date + '&end-date=' + end_date)
            .then(response => response.text())
            .then(data => {
                // Update status message
                document.getElementById('status-message').innerHTML = "Files downloaded";

                // Error messages from script execution will be passed over stdout, therefore if any data is 
                // returned, display this as an error message
                if (data != ""){
                    document.getElementById('error-box').style.display = 'block';
                    document.getElementById('error-message').innerHTML = "An error occurred: " + data;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('error-box').style.display = 'block';
                document.getElementById('error-message').innerHTML = "An error occurred: " + error.message;
            });
    </script>
</body>
</html>
