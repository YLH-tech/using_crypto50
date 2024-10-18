document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('dark-switch');
    const themeStylesheet = document.getElementById('themeStylesheet');

    // Check local storage for the preferred theme
    const preferredTheme = localStorage.getItem('theme');
    if (preferredTheme) {
        themeStylesheet.setAttribute('href', `${preferredTheme}-mode.css`);
        toggleButton.textContent = preferredTheme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }

    toggleButton.addEventListener('click', () => {
        // Toggle the theme
        if (themeStylesheet.getAttribute('href') === 'light-mode.css') {
            themeStylesheet.setAttribute('href', 'dark-mode.css');
            toggleButton.textContent = 'Switch to Light Mode';
            localStorage.setItem('theme', 'dark');
        } else {
            themeStylesheet.setAttribute('href', 'light-mode.css');
            toggleButton.textContent = 'Switch to Dark Mode';
            localStorage.setItem('theme', 'light');
        }
    });
});
