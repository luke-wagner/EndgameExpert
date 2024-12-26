<?php
// Include the database functions
require_once realpath(path: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'database_connector.php');

$session_id = create_new_session();
echo $session_id;
?>