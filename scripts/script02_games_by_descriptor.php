<?php
// Include the database functions
require_once __DIR__ . '..\..\helpers\database_connector.php';

$descriptor = escapeshellarg($_GET['descriptor']);
$descriptor = str_replace('"', "", $descriptor);

$username = escapeshellarg($_GET['username']);
$start_date = escapeshellarg($_GET['start_date']);
$end_date = escapeshellarg($_GET['end_date']);

$games = fetch_games_by_descriptor($username, $start_date, $end_date, $descriptor);
echo $games;
?>