<?php
    // Determine the operating system and set the Python executable
    // DigitalOcean droplet will run in a virtual environment, so must provide explicit path
    // to this binary
    if (PHP_OS_FAMILY === 'Linux') {
        $python_exec = '/var/www/venv/bin/python3';
    } else {
        // Assume Windows as the fallback
        $python_exec = 'python3';
    }

    $filename = basename(__FILE__, '.php'); 
    $python_script = realpath(dirname(__FILE__) . '/../python/evaluate_fens.py');
    $outfilepath = realpath(dirname(__FILE__) . '/../out') . '/' . $filename . '.log';    

    // Get form data
    $session_id = escapeshellarg($_GET['session']);

    // Define the command to call the Python script with arguments
    $command1 = "$python_exec $python_script $session_id 2>&1 1> $outfilepath";

    // Execute the command and capture the output
    $output1 = shell_exec($command1);

    echo $output1;
    exit();
?>