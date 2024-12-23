<?php
    $filename = basename(__FILE__, '.php'); 
    $python_script = realpath(dirname(__FILE__) . '/../python/fetch_games.py');
    $outfilepath = realpath(dirname(__FILE__) . '/../out') . '/' . $filename . '.log';    

    // Get form data
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Define the command to call the Python script with arguments
    $command1 = "python3 $python_script $ccom_username $start_date $end_date 2>&1 1> $outfilepath";

    // Execute the command and capture the output
    $output1 = shell_exec($command1);

    echo $output1;
    exit();
?>