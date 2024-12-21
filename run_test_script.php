<?php
    // Define the command to call the Python script with arguments
    $command1 = "python3 make-requests.py";

    // Execute the command and capture the output
    $output1 = shell_exec($command1);
    //$output2 = shell_exec($command2);
    //$output3 = shell_exec($command3);

    echo $output1;
    exit();
?>
