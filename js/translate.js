// Function to load the translation files for the selected language
async function loadTranslation(lang) {
    const urls = [
        `../../translations/${lang}.json`, // Primary translation file
        `../../translations/user_function/${lang}.json` // Additional translation file
    ];

    try {
        // Load all translation files
        const translationsList = await Promise.all(
            urls.map(url =>
                fetch(url)
                    .then(response => response.json())
                    .catch(error => {
                        console.error(`Error loading translation file from ${url}:`, error);
                        return {}; // Return an empty object in case of an error
                    })
            )
        );

        // Merge all translations
        const mergedTranslations = Object.assign({}, ...translationsList);

        // Apply the translations
        applyTranslations(mergedTranslations);

        return mergedTranslations; // Return the merged translations for further use
    } catch (error) {
        console.error("Error loading translations:", error);
    }
}

// Function to apply translations to elements with the 'data-translate' attribute
function applyTranslations(translations) {
    const elements = document.querySelectorAll("[data-translate]");
    elements.forEach(element => {
        const key = element.getAttribute("data-translate");
        if (translations[key]) {
            if (element.tagName.toLowerCase() === "input" && element.type === "submit") {
                element.value = translations[key]; // Update button text
            } else if (element.placeholder) {
                element.placeholder = translations[key]; // Update input placeholder
            } else {
                element.innerHTML = translations[key]; // Update regular text content
            }
        }
    });
}

// Run this when the page loads
window.onload = function () {
    // Retrieve the selected language from localStorage or default to 'en'
    const savedLanguage = localStorage.getItem('selectedLanguage') || 'en';

    // Load and apply translations for the saved language
    loadTranslation(savedLanguage);

    // Set the dropdown to match the selected language
    const languageSelector = document.getElementById('language-selector');
    if (languageSelector) {
        languageSelector.value = savedLanguage;

        // Listen for language selection changes
        languageSelector.addEventListener('change', function () {
            const selectedLanguage = this.value;
            localStorage.setItem('selectedLanguage', selectedLanguage); // Save to localStorage
            loadTranslation(selectedLanguage);
        });
    }
};

// Example: Confirm clear record using translations
// async function confirmClearRecord() {
//     const confirmationMessage = await getTranslatedText('clear_record_confirmation');
//     return confirm(confirmationMessage);
// }

// Function to get the translated text for a specific key
async function getTranslatedText(key) {
    const language = localStorage.getItem('selectedLanguage') || 'en';
    const translations = await loadTranslation(language);
    return translations[key] || key; // Fallback to key if translation not found
}
