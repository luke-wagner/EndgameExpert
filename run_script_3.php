<?php
    // Get form data
    $ccom_username = escapeshellarg($_GET['username']);
    $start_date = escapeshellarg($_GET['start-date']);
    $end_date = escapeshellarg($_GET['end-date']);

    // Define the command to call the Python script with arguments
    $command1 = "python download_files.py $ccom_username $start_date $end_date";
    $command2 = "python clear_db.py";
    $command3 = "python parse_files.py $ccom_username";

    // Execute the command and capture the output
    //$output1 = shell_exec($command1);
    //$output2 = shell_exec($command2);
    $output3 = shell_exec($command3);

    echo $output3;
    exit();
?>
