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