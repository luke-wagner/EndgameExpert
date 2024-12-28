<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

// Get POST data
$session_id = $_POST['session'];
$username = $_POST['username'];
$start_date = $_POST['start-date'];
$end_date = $_POST['end-date'];

try {
    // Get number of months in between start and end date not accounted for in session_data
    // If greater than 0, we will need to re-run fetch_games.py to account for all months
    $num_occurences = get_not_in_range($session_id, $username, $start_date, $end_date);

    // Build base redirect URL
    $redirect_url = 'analysis.php?session=' . $session_id . '&';

    // Add fetch-data parameter if needed
    if ($num_occurences > 0) {
        $redirect_url .= 'fetch-data=true&';
    }

    // Add rest of parameters
    $redirect_url .= 'username=' . urlencode($username) . '&' .
                     'start-date=' . urlencode($start_date) . '&' .
                     'end-date=' . urlencode($end_date);

    // Output JavaScript to update URL and reload
    echo "const newUrl = '../" . $redirect_url . "'; 
          history.replaceState(null, '', newUrl); 
          window.location.reload();";
    exit();
} catch (PDOException $e) {
    // Handle database errors
    error_log("Database Error: " . $e->getMessage());
    echo "history.replaceState(null, '', 'error.php'); 
          window.location.reload();";
    exit();
}
?>
