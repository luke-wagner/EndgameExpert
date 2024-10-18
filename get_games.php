<?php
$descriptor = escapeshellarg($_GET['descriptor']);
$descriptor = str_replace('"', "", $descriptor);

// Read in login credentials from .env file
$env = parse_ini_file('.env');
$db_username = $env["DB_USERNAME"];
$db_pwd = $env["DB_PWD"];

$servername = "localhost";  // Update with your server details
$dbname = "chessapp";

// Create connection
$conn = new mysqli($servername, $db_username, $db_pwd, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data
$sql = "
with a as (
    select game_link, MAX(move_number) as move_number
    from fens
    where descriptor = ?
    group by game_link
)
	select f.fen, f.game_link from a
    inner join fens f on (f.game_link = a.game_link and f.move_number = a.move_number)
    ";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $descriptor);
$stmt->execute();
$result = $stmt->get_result();

$games = array();
while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}

echo json_encode($games);

$stmt->close();
$conn->close();
?>