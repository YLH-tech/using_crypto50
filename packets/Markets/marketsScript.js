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

// Initialize tables
window.onload = function() {
    updateMarketCaps();
    setInterval(updateMarketCaps, 60000); // Update market caps every minute
};

// Update the market cap data
async function fetchMarketCap() {
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
}

// Function to initialize or update the Top Gainers table
function initializeTopGainersTable() {
    const tbody = document.getElementById('gainers-table-body');
    tbody.innerHTML = ''; // Clear the table before populating it

    const topGainers = coins.filter(coin => changes[coin.id] > 0).sort((a, b) => changes[b.id] - changes[a.id]);
    
    topGainers.forEach(coin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="gainer-price-${coin.id}">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] > 0 ? 'price-change-positive' : 'price-change-negative'}" id="gainer-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="gainer-marketcap-${coin.id}">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
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
            <td colspan="4">
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="loser-price-${coin.id}">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] < 0 ? 'price-change-negative' : 'price-change-positive'}" id="loser-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="loser-marketcap-${coin.id}">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
    });
}

// Function to initialize or update the MarketCap table
function initializeMarketCapTable() {
    const tbody = document.getElementById('marketcap-table-body');
    tbody.innerHTML = ''; // Clear the table before populating it

    coins.forEach(coin => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="4">
                <a href="coin-details.html?id=${coin.id}" class="table-link">
                    <img src="images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo"> ${coin.name} (${coin.id})
                </a>
            </td>
            <td id="market-cap-price-${coin.id}">$${prices[coin.id] || '--'}</td>
            <td class="${changes[coin.id] > 0 ? 'price-change-positive' : 'price-change-negative'}" id="market-cap-change-${coin.id}">${changes[coin.id] || '0.00'}%</td>
            <td id="market-cap-${coin.id}">${marketCaps[coin.id]}</td>
        `;
        tbody.appendChild(row);
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
    const message = JSON.parse(event.data);
    const streamData = message.data;
    const coin = coins.find(c => c.symbol.toLowerCase() === streamData.s.toLowerCase());

    if (coin) {
        prices[coin.id] = parseFloat(streamData.c).toFixed(2); // Current price
        changes[coin.id] = parseFloat(streamData.P).toFixed(2); // 24h percentage change
    }
};

// Process queued updates every 500ms to reduce reflows
setInterval(() => {
    initializeTopGainersTable();
    initializeTopLosersTable();
    initializeMarketCapTable();
}, 500);
