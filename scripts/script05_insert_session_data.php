<?php
    // Include the database functions
    require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

    // Get form data
    $session_id = escapeshellarg($_GET['session']);
    $username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Get rid of double quotes around arguments
    $session_id = str_replace('"', '', $session_id);
    $username = str_replace('"', '', $username);
    $start_date = str_replace('"', '', $start_date);
    $end_date = str_replace('"', '', $end_date);

    echo insert_session_data((int) $session_id, $username, $start_date, $end_date);
?>