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

        // Set initial status message
        document.getElementById('status-message').innerHTML = "Fetching game data...";

        fetch('scripts/script01_fetch_games.php?username=' + ccom_username + '&start-date=' + start_date + '&end-date=' + end_date)
            .then(response => response.text())
            .then(data => {
                // Error messages from script execution will be passed over stdout, therefore if any data is 
                // returned, display this as an error message
                if (data != ""){
                    // Error from script execution
                    document.getElementById('error-box').style.display = 'block';
                    document.getElementById('error-message').innerHTML = "An error occurred: " + data;
                } else {    
                    // Update status message
                    document.getElementById('status-message').innerHTML = "Game data fetched";

                    // Redirect to stats_view
                    // Delete fetch_data param and then refresh page
                    // Also using replaceState so that when back arrow is clicked, it goes back to launch page
                    var url = new URL(window.location.href);
                    var queryParams = new URLSearchParams(url.search);
                    queryParams.delete('fetch-data'); // delete this param
                    url.search = queryParams.toString();
                    history.replaceState(null, null, url.toString());
                    location.reload();
                }

            })
            .catch(error => {
                // Error from HTTP request
                console.error('Error:', error);
                document.getElementById('error-box').style.display = 'block';
                document.getElementById('error-message').innerHTML = "An error occurred: " + error.message;
            });
    </script>
</body>
</html>
