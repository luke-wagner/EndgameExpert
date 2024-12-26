<?php

function connect_to_db() {
    $envpath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env');
    $env = parse_ini_file(filename: $envpath);
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

function create_new_session() {
    $conn = connect_to_db();

    try {
        // Prepare and execute the insert query
        $sql = "INSERT INTO `session` () VALUES ()";
        if ($conn->query($sql) === TRUE) {
            // Get the last inserted ID
            $sessionId = $conn->insert_id;
    
            // Return the session ID
            return $sessionId;
        } else {
            throw new Exception("Error inserting session: " . $conn->error);
        }
    } catch (Exception $e) {
        // Handle errors
        echo $e->getMessage();
        exit;
    } finally {
        // Close the connection
        $conn->close();
    }
}

// Insert/update session data
// This is used to keep track of which data has already been processed
function insert_session_data($session_id, $username, $start_date, $end_date) {
    $conn = connect_to_db();

    try {
        // Prepare the insert query
        $sql = "
        INSERT INTO session_data (session_id, username, min_date, max_date)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            min_date = VALUES(min_date),
            max_date = VALUES(max_date);
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        // Bind the parameters
        $stmt->bind_param("isss", $session_id, $username, $start_date, $end_date);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Insertion successful";
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Handle errors
        echo $e->getMessage();
        exit;
    } finally {
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}

function get_min_date_for_user($username){
    $conn = connect_to_db();

    // SQL query to fetch data
    $sql = "SELECT MIN(CONCAT(year, month)) as min_date FROM game_data WHERE player_name = '$username'";

    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function get_max_date_for_user($username){
    $conn = connect_to_db();

    // SQL query to fetch data
    $sql = "SELECT MAX(CONCAT(year, month)) as max_date FROM game_data WHERE player_name = '$username'";

    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function fetch_games_by_descriptor($username, $start_date, $end_date, $descriptor){
    $conn = connect_to_db();

    // SQL query to fetch data
    $sql = "
    with a as (
        select game_link, MAX(move_number) as move_number
        from fens
        where descriptor like $descriptor
        and game_link in (
			SELECT DISTINCT game_link FROM game_data
			WHERE player_name like $username	-- username
			AND concat(year, '-', month) >= SUBSTRING($start_date, 1, 7)  -- start_date
			AND concat(year, '-', month) <= SUBSTRING($end_date, 1, 7)    -- end_date
		)
        group by game_link
    )
        select f.fen, f.game_link, gd.outcome
        from a 
        inner join fens f on (f.game_link = a.game_link and f.move_number = a.move_number)
        inner join game_data gd on (gd.game_link = f.game_link)
    ";

    $result = $conn->query($sql);

    $games = array();
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }

    $conn->close();

    return json_encode($games);
}

function fetch_game_stats($username, $start_date, $end_date) {
    $conn = connect_to_db();

    $sql = "
    WITH a AS (
        SELECT DISTINCT game_link, descriptor FROM fens
        where game_link in (	
			SELECT DISTINCT game_link FROM game_data
			WHERE player_name like $username	-- username
			AND concat(year, '-', month) >= SUBSTRING($start_date, 1, 7)  -- start_date
			AND concat(year, '-', month) <= SUBSTRING($end_date, 1, 7)    -- end_date
		)
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
