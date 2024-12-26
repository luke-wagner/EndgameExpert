<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

// Get POST data
$username = $_POST['username'];
$start_date = $_POST['start-date'];
$end_date = $_POST['end-date'];

// Convert input dates to YYYYMM format for comparison
$start_compare = date('Ym', strtotime($start_date));
$end_compare = date('Ym', strtotime($end_date));

try {
    // Prepare and execute query for minimum date
    $min_result = get_min_date_for_user($username);
    $min_date = $min_result['min_date'];

    // Prepare and execute query for maximum date
    $max_result = get_max_date_for_user($username);
    $max_date = $max_result['max_date'];

    $full_url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

    $url_base = strtok($full_url, '/') . '/';

    // Build base redirect URL
    $redirect_url = $url_base . 'analysis.php?';

    // Add fetch-data parameter if needed
    if ($start_compare < $min_date || $end_compare > $max_date) {
        $redirect_url .= 'fetch-data=true&';
    }

    // Add rest of parameters
    $redirect_url .= 'username=' . urlencode($username) . '&' .
                     'start-date=' . urlencode($start_date) . '&' .
                     'end-date=' . urlencode($end_date);


    echo $redirect_url;
    //header('Location: ' . $redirect_url);
    exit();

} catch (PDOException $e) {
    // Handle database errors
    error_log("Database Error: " . $e->getMessage());
    header('Location: error.php');
    exit();
}
?>