<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endgame Expert</title>
    <link rel="icon" type="image/x-icon" href="/img/knight-icon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/launch_view.css">
</head>
<body>

<h1>Endgame Expert</h1>

<div class="form-container">
    <div class="form-box">
        <form action="analysis.php" method="GET">
            <input type="hidden" name="fetch-data" value="true">
            <input type="hidden" name="session" value="">

            <label for="username">Chess.com username:</label>
            <input type="text" id="username" name="username" required>

            <div class="inline-fields">
                <label for="start-date">Start date:</label>
                <input type="date" id="start-date" name="start-date" required>

                <label for="end-date">End date:</label>
                <input type="date" id="end-date" name="end-date" required>
            </div>

            <label>
                <input type="checkbox" id="remember-me"> Remember Me
                <div class="tooltip-container">
                    <span class="tooltip-icon">?</span>
                    <span class="tooltip-text">
                        This will store your game data on the server, making future retrievals faster. We will never email you.
                    </span>
                </div>
            </label>

            <div id="extra-fields" style="display: none;">
                <div class="inline-fields">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                </div>
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
    document.getElementById("remember-me").addEventListener("change", function () {
        const extraFields = document.getElementById("extra-fields");
        if (this.checked) {
            extraFields.style.display = "block";
        } else {
            extraFields.style.display = "none";
        }
    });

    <?php
    // Create new session if doesn't yet exist
    if (isset($_GET['session']) == false) {
        ?>
        console.log("Creating session...");

        fetch('../scripts/script04_create_session.php') // call script04 to execute
            .then(response => response.text())
            .then(data => {
                console.log("Session id: " + data);

                // Append session ID as parameter to the URL string
                var url = new URL(window.location.href);
                var queryParams = new URLSearchParams(url.search);
                queryParams.append('session', data);
                url.search = queryParams.toString();
                history.replaceState(null, null, url.toString());

                // Set form element with session ID
                document.querySelector('input[name="session"]').value = data;
            })
            .catch(error => {
                // Error from HTTP request
                console.error('Error:', error);
            });
    <?php
    // Session already exists, still must set hidden input field in form element with session id
    } else {
    ?>
        // Get the session id from the URL
        var url = new URL(window.location.href);
        var queryParams = new URLSearchParams(url.search);
        var session_id = queryParams.get('session')

        // Set form element with session ID
        document.querySelector('input[name="session"]').value = session_id;
    <?php
    }?>
</script>

</body>
</html>