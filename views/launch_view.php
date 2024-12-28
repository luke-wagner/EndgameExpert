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
        <form id="main-form" action="analysis.php" method="GET">
        <input type="hidden" name="fetch-data" value="true">
        <input type="hidden" name="session" value="">

        <label for="username">Chess.com username:</label>
        <input type="text" id="username" name="username" required>

        <div class="inline-fields">
            <label for="start-date">Start date:</label>
            <div>
                <input id="start-date" name="start-date" type="date" />
            </div>
            <label for="end-date">End date:</label>
            <div>
                <input id="end-date" name="end-date" type="date" />
            </div>
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
                <div>
                    <input id="name" type="text" />
                </div>
                <label for="email">Email:</label>
                <div>
                    <input id="email" type="email" />
                </div>
            </div>
        </div>

        <button type="submit">Submit</button>
    </form>
    </div>
</div>

<script>
    // WIP     --------------------------------------------------------------

    // Function to save form values to localStorage
    function saveFormValues() {
        const fields = document.querySelectorAll('input[type="text"], input[type="date"], input[type="email"]');
        fields.forEach(field => {
            localStorage.setItem(field.id, field.value); // Store field value using its ID as the key
        });
    }

    // Function to populate form values from localStorage
    function populateFormValues() {
        const fields = document.querySelectorAll('input[type="text"], input[type="date"], input[type="email"]');
        fields.forEach(field => {
            const savedValue = localStorage.getItem(field.id); // Retrieve value from localStorage
            if (savedValue !== null) {
                field.value = savedValue; // Set the field's value
            }
        });

        // Check if 'name' and 'email' are in localStorage, then check "Remember Me" and show extra fields
        const name = localStorage.getItem('name');
        const email = localStorage.getItem('email');
        const rememberMeCheckbox = document.getElementById("remember-me");
        const extraFields = document.getElementById("extra-fields");

        if (name && email) {
            rememberMeCheckbox.checked = true;  // Check the "Remember Me" checkbox
            extraFields.style.display = "block"; // Show the extra fields for name and email
        }
    }

    // Add event listeners to save values on input change
    document.addEventListener('DOMContentLoaded', () => {
        populateFormValues(); // Populate values when the page loads

        const fields = document.querySelectorAll('input[type="text"], input[type="date"], input[type="email"]');
        fields.forEach(field => {
            field.addEventListener('change', saveFormValues); // Save values on change
        });
    });

    // ----------------------------------------------------------------------    

    document.getElementById("remember-me").addEventListener("change", function () {
        const extraFields = document.getElementById("extra-fields");
        if (this.checked) {
            extraFields.style.display = "block";
        } else {
            extraFields.style.display = "none";
        }
    });

    document.getElementById("main-form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        const form = this;
        const session = document.querySelector('input[name="session"]').value;
        const name = form['name'].value;
        const email = form['email'].value;

        // Construct the URL with query parameters
        const queryParams = new URLSearchParams({
            session: session,
            name: name,
            email: email
        });

        fetch(`../scripts/script05_update_session.php?${queryParams.toString()}`)
            .then(response => response.text())
            .then(data => {
                //console.log(data);

                // Continue with form submission
                form.submit();
            })
            .catch(error => {
                console.error('Error running PHP script:', error);

                // Optionally submit the form even on error
                form.submit();
            });
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