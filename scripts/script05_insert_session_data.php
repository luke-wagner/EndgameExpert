<?php
    // Include the database functions
    require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

    // Get form data
    $session_id = $_GET['session'];
    $username = $_GET['username'];
    $start_date = $_GET['start-date'];
    $end_date = $_GET['end-date'];

    echo insert_session_data((int) $session_id, $username, $start_date, $end_date);
?>