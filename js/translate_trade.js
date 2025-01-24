// This runs when the page loads
window.onload = function() {
    const savedLanguage = localStorage.getItem('selectedLanguage') || 'en';

    // Load and apply translations
    loadTranslation(savedLanguage).then(() => applyTranslations());

    // Handle language selector changes
    const languageSelector = document.getElementById('language-selector');
    if (languageSelector) {
        languageSelector.value = savedLanguage;
        languageSelector.addEventListener('change', function () {
            const selectedLanguage = this.value;
            localStorage.setItem('selectedLanguage', selectedLanguage);
            loadTranslation(selectedLanguage).then(() => applyTranslations());
        });
    }
};

// Function to get the translated text for a specific key
async function getTranslatedText(key) {
    const language = localStorage.getItem('selectedLanguage') || 'en';
    const translations = await loadTranslation(language);
    return translations[key] || key; // Fallback to key if translation not found
}

// Function to load the translation file
let currentTranslations = {};
function loadTranslation(lang) {
    const url = `../../translations/${lang}.json`;
    return fetch(url)
        .then(response => response.json())
        .then(translations => {
            currentTranslations = translations;
            return translations;
        })
        .catch(error => {
            console.error("Error loading translations:", error);
            currentTranslations = {};
        });
}

// Function to apply translations to elements with data-translate and data-translate-placeholder
function applyTranslations() {
    // Apply translations for elements with data-translate
    document.querySelectorAll('[data-translate]').forEach(element => {
        const translationKey = element.getAttribute('data-translate');
        const translatedText = currentTranslations[translationKey] || element.textContent;
        element.textContent = translatedText;
    });

    // Apply translations for elements with data-translate-placeholder
    document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
        const translationKey = element.getAttribute('data-translate-placeholder');
        const translatedText = currentTranslations[translationKey] || element.placeholder;
        element.placeholder = translatedText;
    });
}

// Example: Confirm clear record using translations
// async function confirmClearRecord() {
//     const confirmationMessage = await getTranslatedText('clear_record_confirmation');
//     return confirm(confirmationMessage);
// }