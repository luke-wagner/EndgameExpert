<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

// Grab the parameters from the URL
$session_id = isset($_GET['session']) ? $_GET['session'] : null;
$client_name = isset($_GET['name']) ? $_GET['name'] : null;
$client_email = isset($_GET['email']) ? $_GET['email'] : null;


if ($session_id && $client_name && $client_email) {
    echo "Updating session info...";

    update_session_info($session_id, $client_name, $client_email);
}
?>