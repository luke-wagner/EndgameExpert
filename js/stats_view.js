// Get the session id from the URL
var url = new URL(window.location.href);
var queryParams = new URLSearchParams(url.search);
var session_id = queryParams.get('session') 

// set session id in hidden input field
document.querySelector('input[name="session"]').value = session_id;

// override default behavior of timeframe form; tie to custom handler function
var form = document.getElementById("timeframe-form");
function handleForm(event) {
    event.preventDefault(); // prevent window refresh

     // Collect form data
    var formData = new FormData(event.target);

    // Make a fetch call with URL-encoded data
    fetch('../scripts/script03_update_timeframe.php', {
        method: 'POST',
        body: new URLSearchParams(formData),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);

        // Execute the JavaScript that was returned by PHP
        eval(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
form.addEventListener('submit', handleForm);