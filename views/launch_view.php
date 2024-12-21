<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/launch_view.css">
</head>
<body>

<h1>Endgame Expert</h1>

<div class="form-container">
    <div class="form-box">
        <form action="analysis.php" method="GET">
            <!-- Use a hidden input field to tell page to fetch data on load -->
            <input type="hidden" name="fetch-data" value="true">

            <label for="username">Chess.com username:</label>
            <input type="text" id="username" name="username" required>

            <label for="start-date">Start date:</label>
            <input type="date" id="start-date" name="start-date" required>

            <label for="end-date">End date:</label>
            <input type="date" id="end-date" name="end-date" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

</body>
</html>