const coins = [
    { name: "Bitcoin", symbol: "BTCUSDT", id: "BTC", geckoId: "bitcoin" },
    { name: "Ethereum", symbol: "ETHUSDT", id: "ETH", geckoId: "ethereum" },
    { name: "Binance Coin", symbol: "BNBUSDT", id: "BNB", geckoId: "binancecoin" },
    { name: "XRP", symbol: "XRPUSDT", id: "XRP", geckoId: "ripple" },
    { name: "Dogecoin", symbol: "DOGEUSDT", id: "DOGE", geckoId: "dogecoin" },
    { name: "Solana", symbol: "SOLUSDT", id: "SOL", geckoId: "solana" },
    { name: "Cardano", symbol: "ADAUSDT", id: "ADA", geckoId: "cardano" },
    { name: "Tron", symbol: "TRXUSDT", id: "TRX", geckoId: "tron" },
    { name: "Polkadot", symbol: "DOTUSDT", id: "DOT", geckoId: "polkadot" },
    { name: "Litecoin", symbol: "LTCUSDT", id: "LTC", geckoId: "litecoin" },
    { name: "Bitcoin Cash", symbol: "BCHUSDT", id: "BCH", geckoId: "bitcoin-cash" },
    { name: "Ethereum Classic", symbol: "ETCUSDT", id: "ETC", geckoId: "ethereum-classic" },
    { name: "Uniswap", symbol: "UNIUSDT", id: "UNI", geckoId: "uniswap" },
    { name: "Chainlink", symbol: "LINKUSDT", id: "LINK", geckoId: "chainlink" },
    { name: "Avalanche", symbol: "AVAXUSDT", id: "AVA", geckoId: "avalanche-2" },
    { name: "Neo", symbol: "NEOUSDT", id: "NEO", geckoId: "neo" },
    { name: "EOS", symbol: "EOSUSDT", id: "EOS", geckoId: "eos" },
    { name: "Arbitrum", symbol: "ARBUSDT", id: "ARB", geckoId: "arbitrum" },
    { name: "Aptos", symbol: "APTUSDT", id: "APT", geckoId: "aptos" },
    { name: "Toncoin", symbol: "TONUSDT", id: "TON", geckoId: "the-open-network" }
];

// Global objects to hold prices, changes, and market caps
let prices = {};
let changes = {};
let marketCaps = {};

// Function to update the market cap data
const fetchMarketCap = async () => {
    const geckoIds = coins.map(coin => coin.geckoId).join(',');
    try {
        const response = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${geckoIds}&vs_currencies=usd&include_market_cap=true`);
        const data = await response.json();
        coins.forEach(coin => {
            const marketCap = data[coin.geckoId]?.usd_market_cap;
            marketCaps[coin.id] = marketCap ? (marketCap / 1e6).toFixed(2) + 'M' : 'N/A';
        });
    } catch (error) {
        console.error('Error fetching market cap data:', error);
    }
};

// Initialize tables and start updating
setInterval(updateMarketCaps, 60000); // Update market caps every minute
updateMarketCaps(); // Initial call to update

// Function to initialize or update the Top Gainers table
function initializeTopGainersTable() {
    const tbody = document.getElementById('gainers-table-body');
    tbody.innerHTML = ''; // Clear the table before populating it

    const topGainers = coins.filter(coin => changes[coin.id] > 0).sort((a, b) => changes[b.id] - changes[a.id]);
    
    topGainers.forEach(coin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="crypto-name">
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="./assets/images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo inline-block"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="gainer-price-${coin.id}" class="text-right">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] > 0 ? 'price-change-positive' : 'price-change-negative'} text-right" id="gainer-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="gainer-marketcap-${coin.id}" class="text-right">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
        if(changes[coin.id] > 0) {
            document.getElementById(`gainer-change-${coin.id}`).style.color = "green";
        } else {
            document.getElementById(`gainer-change-${coin.id}`).style.color = "red";
        }
    });
}

// Function to initialize or update the Top Losers table
function initializeTopLosersTable() {
    const tbody = document.getElementById('loser-table-body');
    tbody.innerHTML = ''; // Clear the table before populating it

    const topLosers = coins.filter(coin => changes[coin.id] < 0).sort((a, b) => changes[a.id] - changes[b.id]);

    topLosers.forEach(coin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="crypto-name">
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="./assets/images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo inline-block"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="loser-price-${coin.id}" class="text-right">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] < 0 ? 'price-change-negative' : 'price-change-positive'} text-right" id="loser-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="loser-marketcap-${coin.id}" class="text-right">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
        if(changes[coin.id] > 0) {
            document.getElementById(`loser-change-${coin.id}`).style.color = "green";
        } else {
            document.getElementById(`loser-change-${coin.id}`).style.color = "red";
        }
    });
}

// Function to initialize or update the MarketCap table
function initializeMarketCapTable() {
    const tbody = document.getElementById('marketcap-table-body');
    tbody.innerHTML = ''; // Clear the table before populating it

    coins.forEach(coin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="crypto-name">
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="./assets/images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo inline-block"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="market-cap-price-${coin.id}" class="text-right">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] > 0 ? 'price-change-positive' : 'price-change-negative'} text-right" id="market-cap-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="market-cap-${coin.id}" class="text-right">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
        if(changes[coin.id] > 0) {
            document.getElementById(`market-cap-change-${coin.id}`).style.color = "green";
        } else {
            document.getElementById(`market-cap-change-${coin.id}`).style.color = "red";
        }
    });
}

// Function to update tables every 30 seconds
async function updateMarketCaps() {
    await fetchMarketCap();
    initializeTopGainersTable();
    initializeTopLosersTable();
    initializeMarketCapTable();
}

// Connect to Binance WebSocket for real-time prices
const binanceSocket = new WebSocket('wss://stream.binance.com:9443/stream?streams=' + coins.map(c => `${c.symbol.toLowerCase()}@ticker`).join('/'));

binanceSocket.onmessage = function(event) {
    try {
        const message = JSON.parse(event.data);
        const streamData = message.data;
        const coin = coins.find(c => c.symbol.toLowerCase() === streamData.s.toLowerCase());

        if (coin) {
            prices[coin.id] = parseFloat(streamData.c).toFixed(2); // Current price
            changes[coin.id] = parseFloat(streamData.P).toFixed(2); // 24h percentage change
        }

        let change = changes[coin.id]; // Use the correct variable
        let changeValue;
        if (change > 0) {
            changeValue = `+${change}`;
        } else {
            changeValue = change;
        }

        // Check if the element exists before trying to update it
        const changeElement = document.getElementById(`change-${coin.id}`);
        if (changeElement) {
            changeElement.innerHTML = changeValue + "%";
            changeElement.style.color = change < 0 ? 'red' : 'green';
        } 
    } catch (error) {
        // Log the error in case something unexpected happens, but don't stop execution
        console.error("Error processing WebSocket message:", error);
    }
};




// Process queued updates every 500ms to reduce reflows
setInterval(() => {
    initializeTopGainersTable();
    initializeTopLosersTable();
    initializeMarketCapTable();
}, 500);


// for marketBarGraph

const fetchMarketData = async () => {
    try {
        const historicalDataPromises = coins.map(coin =>
            fetch(`https://api.binance.com/api/v3/klines?symbol=${coin.symbol}&interval=1M&limit=12`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => ({
                    name: coin.name,
                    prices: data.map(item => parseFloat(item[4])), // Closing prices
                }))
                .catch(error => {
                    console.error(`Error fetching market data for ${coin.name}:`, error);
                    return null; // Return null if there's an error
                })
        );

        return Promise.all(historicalDataPromises);
    } catch (error) {
        console.error("Error fetching market data:", error);
        return []; // Return an empty array in case of an error
    }
};


const drawChart = async () => {
    const marketData = await fetchMarketData();
    const filteredData = marketData.filter(data => data !== null); // Remove null entries

    const ctx = document.getElementById('cryptoChart').getContext('2d');

    const translations = await loadTranslation(localStorage.getItem('selectedLanguage') || 'en'); // Get translations for the selected language

    const datasets = filteredData.map(data => ({
        label: data.name,
        data: data.prices,
        borderColor: getRandomColor(),
        backgroundColor: 'rgba(0, 0, 0, 0.1)', // Slightly transparent fill
        fill: true,
        tension: 0.2,
        pointRadius: 6, // Increased point size
        pointHoverRadius: 8 // Increased hover point size
    }));

    const lastYearDates = Array.from({ length: 12 }, (_, i) => {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        return date.toLocaleString('default', { month: 'long' }); // Get full month name
    }).reverse(); // Reverse to have the most recent month on the right

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: lastYearDates,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true, // Allow the chart to fill its container
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14, // Increase font size
                        },
                    },
                },
                title: {
                    display: true,
                    text: translations.chartTitle, // Use translated chart title
                    font: {
                        size: 20,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return `${translations.tooltipLabel}: $${tooltipItem.raw.toFixed(2)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: translations.xAxisTitle, // Use translated x-axis title
                        font: {
                            size: 16,
                        },
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12,
                        maxRotation: 0,
                        minRotation: 0,
                    },
                    grid: {
                        display: false, // Hide grid lines for a cleaner look
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: translations.yAxisTitle, // Use translated y-axis title
                        font: {
                            size: 16,
                        },
                    },
                    beginAtZero: true,
                    grid: {
                        color: '#e0e0e0', // Light grid color for better visibility
                    },
                }
            }
        }
    });
};


const getRandomColor = () => {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
};

drawChart(); // Call the function to draw the chart