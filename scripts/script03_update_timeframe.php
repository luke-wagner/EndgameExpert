<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

// Get POST data
$session_id = $_POST['session'];
$username = $_POST['username'];
$start_date = $_POST['start-date'];
$end_date = $_POST['end-date'];

try {
    // Prepare and execute query for minimum date
    $min_result = get_min_date_for_user($session_id, $username);
    $min_date = $min_result['min_date'];

    // Prepare and execute query for maximum date
    $max_result = get_max_date_for_user($session_id, $username);
    $max_date = $max_result['max_date'];

    // Build base redirect URL
    $redirect_url = 'analysis.php?session=' . $session_id . '&';

    // Add fetch-data parameter if needed
    if ($start_date < $min_date || $end_date > $max_date) {
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
