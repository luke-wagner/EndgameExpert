<?php
    // Get form data
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Define the command to call the Python script with arguments
    $command1 = "python3 fetch_games.py $ccom_username $start_date $end_date";

    // Execute the command and capture the output
    $output1 = shell_exec($command1);

    echo $output1;
    exit();
?>
