// Function to fetch real-time 24h price change data from Binance API
async function fetchRealTimeData() {
    const url = "https://api.binance.com/api/v3/ticker/24hr";
    
    // Show loading indicator
    document.getElementById("loading-indicator").style.display = "block";

    const response = await fetch(url);
    const data = await response.json();

    // Object to store the price change distribution
    const distribution = {
        down10: 0,
        down7: 0,
        down5: 0,
        down3: 0,
        down0: 0,
        // zero: 0,
        up0: 0,
        up3: 0,
        up5: 0,
        up7: 0,
        up10: 0
    };

    // Iterate through the data to categorize price changes
    data.forEach(ticker => {
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
        }
        // else if (priceChangePercent === 0) {
        //     distribution.zero++;
        // } 
        else if (priceChangePercent <= 3) {
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

    // Hide loading indicator
    document.getElementById("loading-indicator").style.display = "none";

    return distribution;
}

// Function to update the column heights dynamically based on real data
async function updateDistribution() {
    const data = await fetchRealTimeData();

    // Log the fetched data to check its structure
    console.log("Fetched Data: ", data);

    // Ensure all expected keys are present and have numeric values
    const down10 = Number(data.down10) || 0;
    const down7 = Number(data.down7) || 0;
    const down5 = Number(data.down5) || 0;
    const down3 = Number(data.down3) || 0;
    const down0 = Number(data.down0) || 0;
    const zero = Number(data.zero) || 0;
    const up0 = Number(data.up0) || 0;
    const up3 = Number(data.up3) || 0;
    const up5 = Number(data.up5) || 0;
    const up7 = Number(data.up7) || 0;
    const up10 = Number(data.up10) || 0;

    // Calculate total price down and up values
    const totalDown = down10 + down7 + down5 + down3 + down0 + zero;
    const totalUp = up0 + up3 + up5 + up7 + up10;

    // Log the totals to verify the calculations
    console.log("Total Price Down: ", totalDown);
    console.log("Total Price Up: ", totalUp);

    // Find the max value to normalize heights
    const maxValue = Math.max(down10, down7, down5, down3, down0, zero, up0, up3, up5, up7, up10);
    const maxHeight = 300; // Maximum height for columns

    // Set dynamic heights for each column based on data
    for (let key in data) {
        let column = document.getElementById(key);
        let countElement = document.getElementById(`${key}-count`);

        // Calculate new height based on max value
        let newHeight = maxValue > 0 ? (Number(data[key]) / maxValue) * maxHeight : 0;

        // Set height to at least 10px or calculated height
        column.style.height = Math.max(newHeight, 10) + "px"; // Ensure height is at least 10px

        // Update the count number
        countElement.textContent = Number(data[key]) || 0; // Ensure count is a number
    }

    // Update total price down and up values in the DOM
    document.getElementById('total-price-down').textContent = `Price Down: ${totalDown}`;
    document.getElementById('total-price-up').textContent = `Price Up: ${totalUp}`;
}



// Set the initial height for the columns to 10px
document.querySelectorAll('.column').forEach(column => {
    column.style.height = "10px";
});

// Call update function on load and set an interval to refresh every minute
updateDistribution();
setInterval(updateDistribution, 60000); // Refresh every minute
