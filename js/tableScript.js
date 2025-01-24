// List of coins and their Binance symbols
const coins = [
  { name: "Bitcoin", symbol: "BTCUSDT", id: "BTC", geckoId: "bitcoin" },
  { name: "Ethereum", symbol: "ETHUSDT", id: "ETH", geckoId: "ethereum" },
  { name: "Dogecoin", symbol: "DOGEUSDT", id: "DOGE", geckoId: "dogecoin" },
  { name: "Polkadot", symbol: "DOTUSDT", id: "DOT", geckoId: "polkadot" },
  { name: "Neo", symbol: "NEOUSDT", id: "NEO", geckoId: "neo" },
  { name: "Litecoin", symbol: "LTCUSDT", id: "LTC", geckoId: "litecoin" },
  {
    name: "Bitcoin Cash",
    symbol: "BCHUSDT",
    id: "BCH",
    geckoId: "bitcoin-cash",
  },
  { name: "XRP", symbol: "XRPUSDT", id: "XRP", geckoId: "ripple" },
  {
    name: "Ethereum Classic",
    symbol: "ETCUSDT",
    id: "ETC",
    geckoId: "ethereum-classic",
  },
];

// Function to create a table row for each coin
function createRow(coin) {
  const row = document.createElement("tr");
  row.id = `row-${coin.id}`;

  // Create the row HTML
  row.innerHTML = `
    <td class="w-[400px] md:w-[500px]"><img src="./assets/images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo inline-block"> ${coin.name} (${coin.id})</td>
    <td id="price-${coin.id}">--</td>
    <td id="change-${coin.id}">--</td>
    <td id="marketcap-${coin.id}">--</td>
  `;

  // Add a click event listener to the row to navigate to the new page
  row.addEventListener("click", () => {
    // Navigate to the trade order page with the format coin_id + "USDT".php
    window.location.href = `./php/trade_order/${coin.symbol.toLowerCase()}.php`;
  });

  // Append the row to the table body
  document.getElementById("crypto-body").appendChild(row);
}

// Initialize the table rows for each coin
coins.forEach((coin) => createRow(coin));

// Function to fetch market cap data using CoinGecko API
async function fetchMarketCap() {
  const geckoIds = coins.map((coin) => coin.geckoId).join(",");
  try {
    const response = await fetch(
      `https://api.coingecko.com/api/v3/simple/price?ids=${geckoIds}&vs_currencies=usd&include_market_cap=true`
    );
    const data = await response.json();

    coins.forEach((coin) => {
      const marketCap = data[coin.geckoId]?.usd_market_cap;
      if (marketCap) {
        // Format market cap in millions (M)
        const marketCapInMillions = (marketCap / 1e6).toFixed(2) + "M";
        document.getElementById(`marketcap-${coin.id}`).innerHTML =
          marketCapInMillions;
      }
    });
  } catch (error) {
    // Handle errors silently
  }
}

// Fetch market cap data every 60 seconds
setInterval(fetchMarketCap, 60000);
fetchMarketCap(); // Initial fetch

// Connect to Binance WebSocket for real-time prices
const binanceSocket = new WebSocket(
  "wss://stream.binance.com:9443/stream?streams=" +
    coins.map((c) => `${c.symbol.toLowerCase()}@ticker`).join("/")
);

// Handle incoming WebSocket messages
binanceSocket.onmessage = function (event) {
  const message = JSON.parse(event.data);
  const streamData = message.data;

  // Find the corresponding coin
  const coin = coins.find(
    (c) => c.symbol.toLowerCase() === streamData.s.toLowerCase()
  );

  // Update the price and 24h change in the table
  if (coin) {
    const price = parseFloat(streamData.c).toLocaleString("en-US", {
      style: "currency",
      currency: "USD",
    });
    document.getElementById(`price-${coin.id}`).innerHTML = price;

    const change = parseFloat(streamData.P).toFixed(2);
    let changeValue;
    if (change > 0) {
      changeValue = `+${change}`;
    } else {
      changeValue = change;
    }
    document.getElementById(`change-${coin.id}`).innerHTML = changeValue + "%";
    document.getElementById(`change-${coin.id}`).style.color =
      change < 0 ? "red" : "green";
  }
};

// Error handling for WebSocket
binanceSocket.onerror = function (error) {
  // Handle WebSocket errors silently
};

// Close socket connection on unload
window.onbeforeunload = function () {
  binanceSocket.close();
};
