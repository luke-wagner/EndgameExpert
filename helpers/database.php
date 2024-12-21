<?php

function connect_to_db() {
    $env = parse_ini_file('.env');
    $db_username = $env['DB_USERNAME'];
    $db_pwd = $env['DB_PWD'];
    $servername = "localhost";
    $dbname = "chessapp";

    $conn = new mysqli($servername, $db_username, $db_pwd, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function fetch_game_stats() {
    $conn = connect_to_db();

    $sql = "
    WITH a AS (
        SELECT DISTINCT game_link, descriptor FROM fens
    )
    SELECT a.descriptor, gd.outcome, COUNT(*) AS count
    FROM a
    INNER JOIN game_data gd ON gd.game_link = a.game_link
    WHERE a.descriptor <> ''
    GROUP BY a.descriptor, gd.outcome;
    ";

    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $descriptor = $row['descriptor'];
            $outcome = $row['outcome'];
            $count = $row['count'];

            if (!isset($data[$descriptor])) {
                $data[$descriptor] = ['win' => 0, 'draw' => 0, 'lose' => 0];
            }

            if ($outcome == 1) {
                $data[$descriptor]['win'] = $count;
            } elseif ($outcome == 0) {
                $data[$descriptor]['draw'] = $count;
            } elseif ($outcome == -1) {
                $data[$descriptor]['lose'] = $count;
            }
        }
    }
    $conn->close();
    return $data;
}
