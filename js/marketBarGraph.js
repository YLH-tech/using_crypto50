// Array of your specific coins to display
const targetCoins = [
    "BTCUSDT", "ETHUSDT", "BNBUSDT", "XRPUSDT", "DOGEUSDT",
    "SOLUSDT", "ADAUSDT", "TRXUSDT", "DOTUSDT", "LTCUSDT",
    "BCHUSDT", "ETCUSDT", "UNIUSDT", "LINKUSDT", "AVAXUSDT",
    "NEOUSDT", "EOSUSDT", "ARBUSDT", "APTUSDT", "TONUSDT"
];

// Function to fetch 24h price change data for specified coins from Binance API
async function fetchRealTimeData() {
    const url = "https://api.binance.com/api/v3/ticker/24hr";

    // Show loading indicator
    document.getElementById("loading-indicator").style.display = "block";

    try {
        const response = await fetch(url);
        const data = await response.json();

        // Filter the data for your specific coins only
        const filteredData = data.filter(ticker => targetCoins.includes(ticker.symbol));

        // Log filtered data for debugging
        console.log("Filtered Data: ", filteredData);

        return filteredData;
    } catch (error) {
        console.error("Error fetching data: ", error);
        return []; // Return empty array on error
    } finally {
        // Hide loading indicator
        document.getElementById("loading-indicator").style.display = "none";
    }
}

// Function to update the distribution columns based on the fetched data
async function updateDistribution() {
    const filteredData = await fetchRealTimeData();
    const distribution = {
        down10: 0, down7: 0, down5: 0, down3: 0, down0: 0,
        up0: 0, up3: 0, up5: 0, up7: 0, up10: 0
    };

    filteredData.forEach(ticker => {
        const priceChangePercent = parseFloat(ticker.priceChangePercent);

        if (priceChangePercent <= -10) {
            distribution.down10++;
        } else if (priceChangePercent <= -7) {
            distribution.down7++;
        } else if (priceChangePercent <= -5) {
            distribution.down5++;
        } else if (priceChangePercent <= -3) {
            distribution.down3++;
        } else if (priceChangePercent <= 0) {
            distribution.down0++;
        } else if (priceChangePercent <= 3) {
            distribution.up0++;
        } else if (priceChangePercent <= 5) {
            distribution.up3++;
        } else if (priceChangePercent <= 7) {
            distribution.up5++;
        } else if (priceChangePercent <= 10) {
            distribution.up7++;
        } else {
            distribution.up10++;
        }
    });

    const totalDown = distribution.down10 + distribution.down7 + distribution.down5 + distribution.down3 + distribution.down0;
    const totalUp = distribution.up0 + distribution.up3 + distribution.up5 + distribution.up7 + distribution.up10;

    const maxValue = Math.max(totalDown, totalUp, ...Object.values(distribution));
    const maxHeight = 300;

    for (let key in distribution) {
        let column = document.getElementById(key);
        let countElement = document.getElementById(`${key}-count`);

        let newHeight = maxValue > 0 ? (Number(distribution[key]) / maxValue) * maxHeight : 0;
        column.style.height = Math.max(newHeight, 10) + "px"; 
        countElement.textContent = Number(distribution[key]) || 0;
    }

    // Update translated text with dynamic numbers
    const savedLanguage = localStorage.getItem('selectedLanguage') || 'en';
    loadTranslation(savedLanguage); // Reload translation for the updated content

    // Get the translation strings for price down and price up
    const translations = await loadTranslation(savedLanguage);

    // Dynamically update the text content with the translated string and the counts
    document.getElementById('total-price-down').textContent = `${translations.priceDown} ${totalDown}`;
    document.getElementById('total-price-up').textContent = `${translations.priceUp} ${totalUp}`;
}


// Set the initial height for the columns to 10px
document.querySelectorAll('.column').forEach(column => {
    column.style.height = "10px";
});

// Call update function on load and set an interval to refresh every minute
updateDistribution();
setInterval(updateDistribution, 60000); // Refresh every minute