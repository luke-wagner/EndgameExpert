<?php
    $filename = basename(__FILE__, '.php'); 
    $outfilepath = realpath(dirname(__FILE__) . '/../out') . '/' . $filename . '.log';

    // Get form data
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Try activating the virtual environment -- for DigitalOcean droplet
    $output = shell_exec("source /var/www/venv/bin/activate");
    echo $output;

    // Define the command to call the Python script with arguments
    $command1 = "python3 ../python/fetch_games.py $ccom_username $start_date $end_date 2>&1 1> $outfilepath";

    // Execute the command and capture the output
    $output1 = shell_exec($command1);

    echo $output1;
    exit();
?>