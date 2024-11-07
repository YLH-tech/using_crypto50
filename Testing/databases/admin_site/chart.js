    const marketList = [
        'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT',
        'ADAUSDT', 'TRXUSDT', 'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT',
        'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 'EOSUSDT', 'ARBUSDT',
        'APTUSDT', 'TONUSDT'
    ];

    let symbol = 'btcusdt'; // Initially set to BTCUSDT
    let interval = '1m';
    let binanceSocket = null;
    // const chart = LightweightCharts.createChart(document.getElementById('chart'), {
    //     width: document.getElementById('chart').clientWidth,
    //     height: 500,
    //     layout: {
    //         backgroundColor: '#fff', // Set background color to black
    //         textColor: '#000', // Optional: change text color to white for better contrast
    //         logo: undefined
    //     },
    //     timeScale: {
    //         timeVisible: true,
    //         secondsVisible: false,
    //     },
    // });


    // const candleSeries = chart.addCandlestickSeries();

    async function loadMarketData() {
        document.getElementById('market-list').innerHTML = ''; // Clear existing list
        const uniqueMarkets = [...new Set(marketList)]; // Ensure unique symbols
        for (const coin of uniqueMarkets) {
            try {
                const tickerRes = await fetch(`https://api.binance.com/api/v3/ticker/24hr?symbol=${coin}`);
                const tickerData = await tickerRes.json();
                const priceChangePercent = parseFloat(tickerData.priceChangePercent).toFixed(2);
                const price = parseFloat(tickerData.lastPrice).toFixed(2);

                const li = document.createElement('li');
                li.dataset.symbol = coin.toLowerCase(); // Add a data attribute for the symbol
                li.innerHTML = `<strong>${coin}</strong> <span class="price">$${price}</span> <span class="change">(${priceChangePercent}%)</span>`; // Separate elements for price and change
                
                // Add a click event to navigate to a specific page
                li.addEventListener('click', () => {
                    window.location.href = `Coins/${coin.toLowerCase()}.php`; // Modify this line with your desired link format
                });

                document.getElementById('market-list').appendChild(li);
            } catch (error) {
                console.error('Error fetching market data:', error);
            }
        }
    }


    // Start WebSocket for order book
    // function startOrderBookWebSocket(symbol) {
    //     const orderBookSocketUrl = `wss://stream.binance.com:9443/ws/${symbol}@depth`;
    //     const orderBookSocket = new WebSocket(orderBookSocketUrl);

    //     orderBookSocket.onmessage = function(event) {
    //         const orderBookData = JSON.parse(event.data);

    //         updateOrderBook(orderBookData);
    //     };

    //     orderBookSocket.onerror = function(error) {
    //         console.error("Order Book WebSocket error:", error);
    //     };
    // }

    // Update the order book display
    // Update the order book display
// function updateOrderBook(data) {
//     const orderTableBody = document.querySelector('#orderTable tbody');
//     // Clear existing orders
//     orderTableBody.innerHTML = '';

//     // Process sell orders (asks)
//     const sellOrders = data.asks || []; // Ensure `asks` is an array
//     sellOrders.forEach(order => {
//         const [price, amount] = order;
//         const total = (price * amount).toFixed(2); // Calculate total
//         const row = document.createElement('tr');
//         row.innerHTML = `
//             <td style="color: #dc3545;">$${parseFloat(price).toFixed(2)}</td>
//             <td style="color: #dc3545;">${parseFloat(amount).toFixed(2)}</td>
//             <td style="color: #dc3545;">$${total}</td>
//         `;
//         orderTableBody.appendChild(row);
//     });

//     // Process buy orders (bids)
//     const buyOrders = data.bids || []; // Ensure `bids` is an array
//     buyOrders.forEach(order => {
//         const [price, amount] = order;
//         const total = (price * amount).toFixed(2); // Calculate total
//         const row = document.createElement('tr');
//         row.innerHTML = `
//             <td style="color: #28a745;">$${parseFloat(price).toFixed(2)}</td>
//             <td style="color: #28a745;">${parseFloat(amount).toFixed(2)}</td>
//             <td style="color: #28a745;">$${total}</td>
//         `;
//         orderTableBody.appendChild(row);
//     });

//     // Update current price
//     const currentPriceElement = document.getElementById('current-price-value');
//     currentPriceElement.textContent = data.lastUpdatePrice; // Assuming `lastUpdatePrice` comes from your data
//     currentPriceElement.style.color = data.lastUpdatePriceChange > 0 ? '#28a745' : '#dc3545'; // Change color based on price change
// }


    // Call this function to start the order book WebSocket
    // startOrderBookWebSocket(symbol);

    // Function to load historical data
    // async function loadHistoricalData(symbol, interval) {
    //     const res = await fetch(`https://api.binance.com/api/v3/klines?symbol=${symbol.toUpperCase()}&interval=${interval}&limit=500`);
    //     const data = await res.json();
    //     const chartData = data.map(d => ({
    //         time: d[0] / 1000, // Convert to seconds
    //         open: parseFloat(d[1]),
    //         high: parseFloat(d[2]),
    //         low: parseFloat(d[3]),
    //         close: parseFloat(d[4])
    //     }));

    //     candleSeries.setData(chartData);
    //     updateOHLC(chartData); // Update OHLC display
    //     updateMovingAverage(chartData);
    // }

    // function updateOHLC(data) {
    //     if (data.length > 0) {
    //         const lastCandle = data[0]; // Get the first element since we pass only one
    //         document.getElementById('ohlc-open').textContent = `Open: $${lastCandle.open.toFixed(2)}`;
    //         document.getElementById('ohlc-high').textContent = `High: $${lastCandle.high.toFixed(2)}`;
    //         document.getElementById('ohlc-low').textContent = `Low: $${lastCandle.low.toFixed(2)}`;
    //         document.getElementById('ohlc-close').textContent = `Close: $${lastCandle.close.toFixed(2)}`;
    //     }
    // }

    // Function to calculate and update the Moving Average
    // function updateMovingAverage(data) {
    //     const maPeriod = 10; // Set the MA period
    //     const maData = data.map((d, index) => {
    //         if (index < maPeriod - 1) return null; // Not enough data for MA
    //         const slice = data.slice(index - maPeriod + 1, index + 1);
    //         const sum = slice.reduce((acc, val) => acc + val.close, 0);
    //         return {
    //             time: d.time,
    //             value: sum / maPeriod
    //         };
    //     }).filter(Boolean);
        
    //     maLineSeries.setData(maData); // Only draw MA line
    // }

    // Start real-time WebSocket updates for the selected market symbol
        function startWebSocket(symbol, interval) {
        if (binanceSocket) {
            binanceSocket.close();
        }

        // // Kline WebSocket for candlestick updates
        const socketUrl = `wss://stream.binance.com:9443/ws/${symbol}@kline_${interval}`;
        binanceSocket = new WebSocket(socketUrl);

        // binanceSocket.onmessage = function (event) {
        //     const data = JSON.parse(event.data);
        //     const candlestick = data.k;
        
        //     candleSeries.update({
        //         time: candlestick.t / 1000,
        //         open: parseFloat(candlestick.o),
        //         high: parseFloat(candlestick.h),
        //         low: parseFloat(candlestick.l),
        //         close: parseFloat(candlestick.c),
        //     });
        
        //     // Update the current price in the header
            // document.getElementById('current-price').textContent = `Current Price: $${parseFloat(candlestick.c).toFixed(2)}`;
        
        //     // Update the OHLC display
        //     updateOHLC([{ 
        //         open: parseFloat(candlestick.o), 
        //         high: parseFloat(candlestick.h), 
        //         low: parseFloat(candlestick.l), 
        //         close: parseFloat(candlestick.c) 
        //     }]); // Pass the latest data as an array
        // };
        

        // binanceSocket.onerror = function (error) {
        //     console.error("WebSocket error:", error);
        // };

        // Ticker WebSocket for 24-hour change percentage for all markets
        const tickerSocketUrl = `wss://stream.binance.com:9443/ws/${symbol}@ticker`;
        const tickerSocket = new WebSocket(tickerSocketUrl);

        tickerSocket.onmessage = function (event) {
            const tickerData = JSON.parse(event.data);
            const priceChangePercent = parseFloat(tickerData.P); // Use 'P' for the percentage change
            const newPrice = parseFloat(tickerData.c).toFixed(2); // Latest price from ticker data
            const tickerSymbol = tickerData.s.toLowerCase(); // Symbol in lowercase
            
            // Update the header 24h change for BTCUSDT
            if (tickerSymbol === 'btcusdt') {
                const headerChangeElement = document.getElementById('price-change-info');
                if (headerChangeElement) {
                    headerChangeElement.innerHTML = `24h Change: <strong>${priceChangePercent.toFixed(2)}%</strong>`;
                    
                    const valueElement = headerChangeElement.querySelector('strong');
                    valueElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
                }
            }

            // Update the left sidebar market list for the specific symbol
            const listItem = document.querySelector(`li[data-symbol="${tickerSymbol}"]`);
            if (listItem) {
                const priceElement = listItem.querySelector('.price');
                priceElement.textContent = `$${newPrice}`;

                const changeElement = listItem.querySelector('.change');
                changeElement.textContent = `(${priceChangePercent.toFixed(2)}%)`;

                // Apply color based on the price change percentage
                priceElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
                changeElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
            }
        };

        tickerSocket.onerror = function (error) {
            console.error("Ticker WebSocket error:", error);
        };
    }
    // Add event listener to timeframe buttons
    // document.getElementById('timeframes').addEventListener('click', function (event) {
    //     if (event.target.tagName === 'BUTTON') {
    //         const newInterval = event.target.dataset.interval;

    //         // Update active button class
    //         const buttons = document.querySelectorAll('#timeframes button');
    //         buttons.forEach(button => button.classList.remove('active')); // Remove 'active' class from all buttons
    //         event.target.classList.add('active'); // Add 'active' class to the clicked button

    //         if (newInterval !== interval) {
    //             interval = newInterval;
    //             reloadChartDataAndWebSocket(symbol, interval); // Reload chart data and WebSocket
    //         }
    //     }
    // });

    // function reloadChartDataAndWebSocket(symbol, interval) {
    //     if (binanceSocket) {
    //         binanceSocket.close(); // Close existing WebSocket
    //     }

    //     candleSeries.setData([]); // Clear existing chart data

    //     loadHistoricalData(symbol, interval); // Load new historical data

    //     startWebSocket(symbol, interval); // Start WebSocket with new interval
    // }


    // Initialize WebSocket for all markets
    marketList.forEach(coin => {
        startWebSocket(coin.toLowerCase(), interval);
    });

    // Initial data load
    loadMarketData();
    // loadHistoricalData(symbol, interval);
    // startWebSocket(symbol, interval);

    // Resize chart on window resize
    // window.addEventListener('resize', () => {
    //     chart.resize(document.getElementById('chart').clientWidth, 500);
    // });

    const orderTableBody = document.querySelector('#orderTable tbody');

// Create a row for the current price
const currentPriceRow = document.createElement('tr');
currentPriceRow.className = 'current-price-row'; // Add a class for styling
currentPriceRow.innerHTML = `
    <td colspan="3" style="font-weight: bold; text-align: left; font-size: 16px;">
        <span id="currentPrice" style="font-size: 16px;"></span>
    </td>
`;

// Connecting to Binance WebSocket for BTCUSDT order book
const socket = new WebSocket('wss://stream.binance.com:9443/ws/btcusdt@depth');

let lastPrice = 0;

// Function to fill the order table with the latest data
function fillOrderTable(sellOrders, buyOrders) {
    // Clear the table body except for the current price row
    orderTableBody.innerHTML = '';

    // Fill the sell orders (asks) with price only
    for (let i = 0; i < 10; i++) { // Updated to 10
        const order = sellOrders[i] || [0, 0]; // Fallback to zero
        const price = parseFloat(order[0]);
        const amount = parseFloat(order[1]);
        const total = price * amount;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="color: red; font-weight: bold; font-size: 12px;">${price.toFixed(2)}</td>
            <td style="color: grey; font-weight: bold; font-size: 12px;">${amount.toFixed(5)}</td>
            <td style="color: grey; font-weight: bold; font-size: 12px;">${(total / 1000).toFixed(2)}K</td>
        `;
        orderTableBody.appendChild(row);
    }

    // Insert the current price row between sell and buy orders
    orderTableBody.appendChild(currentPriceRow);

    // Fill the buy orders (bids) with price only
    for (let i = 0; i < 10; i++) { // Updated to 10
        const order = buyOrders[i] || [0, 0]; // Fallback to zero
        const price = parseFloat(order[0]);
        const amount = parseFloat(order[1]);
        const total = price * amount;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="color: green; font-weight: bold; font-size: 12px;">${price.toFixed(2)}</td>
            <td style="color: grey; font-weight: bold; font-size: 12px;">${amount.toFixed(5)}</td>
            <td style="color: grey; font-weight: bold; font-size: 12px;">${(total / 1000).toFixed(2)}K</td>
        `;
        orderTableBody.appendChild(row);
    }
}

socket.onopen = function() {
    console.log("WebSocket connection established");
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log("Parsed data:", data); // Log the parsed data to verify its structure

    // Get the top 10 sell orders (asks)
    const sellOrders = data.a ? data.a.slice(0, 10) : []; // Updated to 10

    // Get the top 10 buy orders (bids)
    const buyOrders = data.b ? data.b.slice(0, 10) : []; // Updated to 10

    // Call function to fill the order table
    fillOrderTable(sellOrders, buyOrders);

    // Update the current price
    const topSellPrice = sellOrders.length > 0 ? parseFloat(sellOrders[0][0]) : null;
    const topBuyPrice = buyOrders.length > 0 ? parseFloat(buyOrders[0][0]) : null;

    if (topSellPrice !== null && topBuyPrice !== null) {
        const currentPrice = (topSellPrice + topBuyPrice) / 2; // Average between top sell and buy
        document.getElementById('currentPrice').textContent = `${currentPrice.toFixed(2)}`;

        if (currentPrice > lastPrice) {
            document.getElementById('currentPrice').style.color = 'green'; // Price increased (buy)
        } else if (currentPrice < lastPrice) {
            document.getElementById('currentPrice').style.color = 'red'; // Price decreased (sell)
        }
        lastPrice = currentPrice; // Store the last price for comparison
    }
};

socket.onerror = function(error) {
    console.error("WebSocket error:", error);
};

socket.onclose = function() {
    console.log("WebSocket connection closed");
};

