<?php

if (isset($_GET['username'], $_GET['start-date'], $_GET['end-date'])) {
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);
    
    include 'views/loading_view.php';
} else {
    //$data = fetch_game_stats();
    //include 'views/stats_view.php';
    include 'views/launch_view.php';
}