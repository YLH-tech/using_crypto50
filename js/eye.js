document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {
        // Get the associated password input field using data-toggle attribute
        const passwordField = document.querySelector(this.getAttribute('data-toggle'));
        
        // Toggle the type attribute
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the eye / eye-slash icon
        this.classList.toggle('fa-eye-slash');
    });
});