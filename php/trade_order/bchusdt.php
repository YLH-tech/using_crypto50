<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /admin_site/login.php");
    exit();
}

// Get user's USDT balance using PDO
$user_id = $_SESSION['user_id'];
$query = "SELECT usdt FROM user_balances WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_balance = $stmt->fetch(PDO::FETCH_ASSOC);
$available_usdt = $user_balance['usdt'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Crypto Market</title>
    <link rel="stylesheet" href="chart.css">
    <!-- <script src="lightweight-charts.standalone.production.js"></script> -->
    <script>
        function loadTradingView() {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = "tv.js";
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Failed to load TradingView script.'));
                document.body.appendChild(script);
            });
        }

        function loadWidget() {
            new TradingView.widget({
                "autosize": true,
                "symbol": "BINANCE:BCHUSDT",
                "interval": "1",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "hide_legend": true,
                "hide_side_toolbar": false,
                "allow_symbol_change": false,
                "save_image": false,
                "details": true,
                "calendar": false,
                "container_id": "tradingview-chart"
            });
        }

        window.addEventListener('load', () => {
            loadTradingView()
                .then(loadWidget)
                .catch(error => {
                    console.error(error);
                    document.getElementById('tradingview-chart').innerText = 'Failed to load chart. Please try again later.';
                });
        });
    </script>
</head>
<body>
    <header>
        <h1>BCHUSDT</h1>
        <div>
            <span id="price-change-info">24h Change: 0.00%</span>
        </div>
    </header>

    <div class="container">
        <aside class="market-list">
            <ul id="market-list">
                <li data-symbol="btcusdt">
                    <strong>BTCUSDT</strong> <span class="price">$0.00</span> <span class="change">(0.00%)</span>
                </li>
                <li data-symbol="ethusdt">
                    <strong>ETHUSDT</strong> <span class="price">$0.00</span> <span class="change">(0.00%)</span>
                </li>
            </ul>
        </aside>

        <main class="chart-area">
            <div class="tradingview-widget-container" style="height:100%; width:100%;">
                <div id="tradingview-chart" style="height: 720px;"></div>
            </div>
            <div class="trade-columns">
                <div class="buy-column">
                    <p>Available Balance(USDT): <span id="available-usdt"><?php echo number_format($available_usdt, 6); ?></span></p>
                    <span id="buy-price">Loading...</span><br><br>  
                    <div id="maxBuyBCH" style="color:grey;">Max Buy BCH: 0.000000</div>
                    <form id="buy-form">                        
                        <label for="buy-amount">Amount (BCH):</label>
                        <input type="number" id="buy-amount" placeholder="Quantity you want to buy" min="0" step="0.000001" oninput="validateAmount('buy')">
                        <div>select period:</div>
                        <table class="option-table">
                            <tr>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">30s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">60s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">120s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">300s</button></td>
                            </tr>
                            <tr>
                                <td><span class="percent-option">40%</span></td>
                                <td><span class="percent-option">50%</span></td>
                                <td><span class="percent-option">70%</span></td>
                                <td><span class="percent-option">100%</span></td>
                            </tr>
                        </table>

                        <button type="submit" id="buy-button" >Buy BCH</button>
                    </form>
                </div>

                <div class="sell-column">
                <p>Available Balance(USDT): <span class="available-usdt"><?php echo number_format($available_usdt, 6); ?></span></p>
                <span id="sell-price">Loading...</span><br><br>
                <div id="maxSellBCH" style="color:grey;">Max Sell BCH: 0.000000</div>
                    <form id="sell-form">
                        
                        <label for="sell-amount">Amount (BCH):</label>
                        <input type="number" id="sell-amount" placeholder="Quantity you want to sell" min="0" step="0.000001" oninput="validateAmount('sell')">
                        <div>select period:</div>
                        <table class="option-table">
                            <tr>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">30s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">60s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">120s</button></td>
                                <td><button type="button" class="time-option" onclick="selectTimeOption(this)">300s</button></td>
                            </tr>
                            <tr>
                                <td><span class="percent-option">40%</span></td>
                                <td><span class="percent-option">50%</span></td>
                                <td><span class="percent-option">70%</span></td>
                                <td><span class="percent-option">100%</span></td>
                            </tr>
                        </table>
                        
                        <button type="submit" id="sell-button" >Sell BCH</button>
                    </form>
                </div>
            </div>
        </main>

        <aside class="order-book">
            <table id="orderTable">
                <thead>
                    <tr>
                        <th>Price (USDT)</th>
                        <th>Amount (BCH)</th>
                        <th>Total (USDT)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </aside>
    </div>

    <!-- Overlay for Order Details -->
    <div id="order-details-overlay">
        <div class="overlay-content">
            <!-- Content will be dynamically inserted here by JavaScript -->
        </div>
    </div>
    <!-- Overlay for Order Confirmation -->
    <div id="order-confirmation-overlay" style="display: none;">
        <div class="overlay-content">
            <h2>Order Confirmation</h2>
            <p>Your order has been successfully completed!</p>
            <button id="close-confirmation">Close</button>
        </div>
    </div>


    <!-- This script is for current_price and Max buy/sell BTC in trade columns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceElement = document.getElementById('buy-price');
            const sellPriceElement = document.getElementById('sell-price');

            function connectWebSocket() {
                const ws = new WebSocket('wss://stream.binance.com:9443/ws/bchusdt@trade'); // Binance BCH/USDT trade stream

                ws.onmessage = event => {
                    const data = JSON.parse(event.data);
                    const currentPrice = parseFloat(data.p).toFixed(2);

                    // Update price displays
                    priceElement.innerText = `Price (USDT): ${currentPrice}`;
                    sellPriceElement.innerText = `Price (USDT): ${currentPrice}`;

                    // Calculate max BTC buyable and sellable based on current price
                    calculateMaxAmounts(currentPrice);
                };

                ws.onerror = error => {
                    console.error('WebSocket Error:', error);
                    setTimeout(connectWebSocket, 5000); // Retry on error
                };

                ws.onclose = () => {
                    console.log('WebSocket disconnected. Reconnecting...');
                    setTimeout(connectWebSocket, 5000); // Reconnect after disconnect
                };
            }

            function calculateMaxAmounts(currentPrice) {
                const availableUsdt = parseFloat(document.querySelector('.available-usdt').innerText.replace(/,/g, '')) || 0;

                // Calculate Max Buy BTC
                const maxBuyBch = (availableUsdt / currentPrice).toFixed(6);
                document.getElementById('maxBuyBCH').innerText = `Max Buy BCH: ${maxBuyBch}`;

                // Calculate Max Sell BTC (also based on available USDT)
                const maxSellBch = (availableUsdt / currentPrice).toFixed(6);
                document.getElementById('maxSellBCH').innerText = `Max Sell BCH: ${maxSellBch}`;
            }
            function validateAmount(type) {
                const amountInput = document.getElementById(type + '-amount');
                const maxAmount = parseFloat(document.getElementById(type === 'buy' ? 'maxBuyBCH' : 'maxSellBCH').innerText.replace('Max ' + (type === 'buy' ? 'Buy' : 'Sell') + ' BCH: ', ''));

                if (parseFloat(amountInput.value) > maxAmount) {
                    amountInput.value = maxAmount.toFixed(6); // Set value to max allowed if exceeded
                    alert(`The amount cannot exceed the maximum ${type === 'buy' ? 'buy' : 'sell'} limit of ${maxAmount} BCH.`);
                }
            }

            connectWebSocket();

        });

        
    </script>
    <!-- This script is for all trade order functions -->
    <script>
    let selectedTimeInterval = null;

    function selectTimeOption(button) {
        const form = button.closest('form');
        const buttonId = form.id === 'buy-form' ? 'buy-button' : 'sell-button';

        // Deselect previously selected button in this row
        form.querySelectorAll('.time-option').forEach(opt => opt.classList.remove('selected'));

        // Select the clicked button and store time interval
        button.classList.add('selected');
        selectedTimeInterval = parseInt(button.innerText) * 1000; // Convert to milliseconds

        // Enable the submit button for the form
        document.getElementById(buttonId).disabled = false;
    }

    function validateForm(event, formId) {
        const form = document.getElementById(formId);
        const selectedOption = form.querySelector('.time-option.selected');
        const amountInput = form.querySelector('input[type="number"]');
        const amount = parseFloat(amountInput.value) || 0;

        // Get max amount for buy or sell
        const maxAmount = parseFloat(document.getElementById(formId === 'buy-form' ? 'maxBuyBCH' : 'maxSellBCH').innerText.replace('Max ' + (formId === 'buy-form' ? 'Buy' : 'Sell') + ' BCH: ', ''));

        //Check for insufficient balance
        if (amount > maxAmount) {
            event.preventDefault();
            alert(`Insufficient Balance.`);
            return;
        }

        // Check if the amount is above the minimum value and below max allowed
        if (amount < 0.05 || amount > maxAmount) {
            event.preventDefault();
            alert(`Please enter an amount between 0.05 BCH and the maximum allowed: ${maxAmount} BCH.`);
            return;
        }

        // Check if a time interval is selected
        if (!selectedOption) {
            event.preventDefault();
            alert("Please select a time interval before proceeding.");
            return;
        }

        // All checks passed, proceed with countdown overlay
        event.preventDefault();
        showCountdownOverlay(formId, amount);
    }


    function showCountdownOverlay(formId, amount) {
        const orderDirection = formId === 'buy-form' ? 'Buy' : 'Sell';
        const symbol = 'BCHUSDT';

        // Fetch and format the current price
        const currentPrice = parseFloat(document.getElementById('buy-price').innerText.replace('Price (USDT): ', '')).toFixed(2);

        // Populate overlay with order information and formatted price
        const overlay = document.getElementById('order-details-overlay');
        overlay.classList.add('active'); // Make overlay visible
        overlay.querySelector('.overlay-content').innerHTML = `
            <h2>${orderDirection} Order</h2>
            <p>Symbol: ${symbol}</p>
            <p>Amount: ${amount} BCH</p>
            <p>Starting Price: $${currentPrice}</p>
            <p id="realTimePrice">Current Price: $${currentPrice}</p>
            <p id="expectedPL">Expected P/L: $0.00</p>
            <canvas id="countdown-timer-circle" width="180" height="180"></canvas>
       `;

        startCountdown(overlay, selectedTimeInterval, currentPrice, amount, orderDirection);
    }


    function startCountdown(overlay, timeRemaining, startPrice, amount, orderDirection) {
        const countdownCanvas = document.getElementById('countdown-timer-circle');
        const ctx = countdownCanvas.getContext('2d');
        const radius = countdownCanvas.width / 2;
        const startAngle = -Math.PI / 2; // Start from the top
        const fullTime = timeRemaining; // Store initial countdown time

        const priceElement = overlay.querySelector('#realTimePrice');
        const plElement = overlay.querySelector('#expectedPL');

        const percentages = {
            30000: 0.40,
            60000: 0.50,
            120000: 0.70,
            300000: 1.00
        };
        const selectedPercentage = percentages[selectedTimeInterval] || 1;

        const ws = new WebSocket('wss://stream.binance.com:9443/ws/bchusdt@trade');
        ws.onmessage = event => {
            const data = JSON.parse(event.data);
            const currentPrice = parseFloat(data.p).toFixed(2);
            priceElement.innerText = `Current Price: $${currentPrice}`;
            const profitLoss = ((currentPrice - startPrice) * amount * selectedPercentage * (orderDirection === 'Buy' ? 1 : -1)).toFixed(6);
            plElement.innerText = `Expected P/L: $${profitLoss}`;
            plElement.style.color = profitLoss >= 0 ? 'limegreen' : 'red';
        };

        function drawCountdownCircle(timeLeft) {
            const progress = timeLeft / fullTime;
            const endAngle = startAngle + 2 * Math.PI * (1 - progress);
            
            // Clear the canvas
            ctx.clearRect(0, 0, countdownCanvas.width, countdownCanvas.height);

            // Draw gradient for progress circle
            const gradient = ctx.createLinearGradient(0, 0, countdownCanvas.width, 0);
            gradient.addColorStop(0, '#007bff');
            gradient.addColorStop(1, '#00c9ff');

            // Draw background circle with shadow
            ctx.beginPath();
            ctx.arc(radius, radius, radius - 10, 0, 2 * Math.PI);
            ctx.lineWidth = 12;
            ctx.strokeStyle = '#333';
            ctx.shadowBlur = 20;
            ctx.shadowColor = 'rgba(0, 123, 255, 0.3)';
            ctx.stroke();
            ctx.shadowBlur = 0;  // Reset shadow

            // Draw progress circle with gradient
            ctx.beginPath();
            ctx.arc(radius, radius, radius - 10, startAngle, endAngle, false);
            ctx.strokeStyle = gradient;
            ctx.lineWidth = 12;
            ctx.stroke();

            // Draw remaining time in the center with glow
            ctx.fillStyle = '#ffffff';
            ctx.font = '24px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0, 123, 255, 0.6)';
            ctx.shadowBlur = 10;
            ctx.fillText((timeLeft / 1000).toFixed(0) + 's', radius, radius);
            ctx.shadowBlur = 0;  // Reset shadow
        }

        const countdownInterval = setInterval(() => {
            timeRemaining -= 1000;
            drawCountdownCircle(timeRemaining);

            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
                ws.close();
                overlay.classList.remove('active'); // Hide the order details overlay
                showOrderConfirmation(symbol, amount, startPrice, orderDirection); // Pass order details
                saveOrderHistory(startPrice, amount, parseFloat(plElement.innerText.split('$')[1]));
            }
        }, 1000);
    }

    function showOrderConfirmation(symbol, amount, startPrice, orderDirection) {
        const confirmationOverlay = document.getElementById('order-confirmation-overlay');
        const endPrice = parseFloat(document.getElementById('realTimePrice').innerText.replace('Current Price: $', '')).toFixed(2);
        
        const selectedPercentage = {
            30000: 0.40,
            60000: 0.50,
            120000: 0.70,
            300000: 1.00
        }[selectedTimeInterval] || 1;
        
        const profitLoss = ((endPrice - startPrice) * amount * selectedPercentage * (orderDirection === 'Buy' ? 1 : -1)).toFixed(6);

        confirmationOverlay.querySelector('.overlay-content').innerHTML = `
            <h2>Order Confirmation</h2>
            <p>Your order has been successfully completed!</p>
            <p>Symbol: ${symbol}</p>
            <p>Amount: ${amount} BCH</p>
            <p>Starting Price: $${startPrice}</p>
            <p>End Price: $${endPrice}</p>
            <p>Profit/Loss: $${profitLoss}</p>
            <button id="close-confirmation">Close</button>
        `;

        confirmationOverlay.style.display = 'flex';

        saveOrderToDatabase(symbol, amount, startPrice, endPrice, profitLoss, orderDirection);

        document.getElementById('close-confirmation').onclick = () => {
            confirmationOverlay.style.display = 'none';
        };
    }

    function saveOrderToDatabase(symbol, amount, startingPrice, endPrice, expectedPL, orderDirection) {
        const orderData = {
            symbol: symbol,
            amount: amount,
            starting_price: startingPrice,
            end_price: endPrice,
            expected_pl: expectedPL,
            order_type: orderDirection // Pass the order type
        };

        fetch('save_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Order saved successfully:', data.message);
                updateUserBalance(expectedPL);
            } else {
                console.error('Error saving order:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }


    function updateUserBalance(profitLoss) {
        fetch('update_balance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ profit_loss: profitLoss }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('User balance updated successfully:', data.message);
                refreshUserBalance(); // Call to fetch the updated balance
            } else {
                console.error('Error updating balance:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }

    function refreshUserBalance() {
        // Fetch the updated balance from the server
        fetch('get_balance.php') // This PHP file should return the updated balance
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the displayed available USDT in both buy and sell columns
                    document.getElementById('available-usdt').innerText = parseFloat(data.balance).toFixed(6);
                    document.querySelector('.available-usdt').innerText = parseFloat(data.balance).toFixed(6);

                    // Calculate max BTC based on updated available USDT
                    const currentPrice = parseFloat(document.getElementById('buy-price').innerText.replace('Price (USDT): ', '').trim());
                    calculateMaxAmounts(currentPrice);
                } else {
                    console.error('Error fetching updated balance:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching balance:', error);
            });
    }

   




    function saveOrderHistory(startPrice, amount, profitLoss) {
        // Placeholder function to save order to history
        console.log("Saving order history:", { startPrice, amount, profitLoss });
        // Implement actual saving to database if needed
    }
    

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('buy-form').addEventListener('submit', function(event) {
            validateForm(event, 'buy-form');
        });

        document.getElementById('sell-form').addEventListener('submit', function(event) {
            validateForm(event, 'sell-form');
        });
    });
</script>

<!-- This script is for left-side(Market list), right-side(ordr book) -->
<script>
        const marketList = [
            'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT',
            'ADAUSDT', 'TRXUSDT', 'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT',
            'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 'EOSUSDT', 'ARBUSDT',
            'APTUSDT', 'TONUSDT'
        ];

        let symbol = 'BCHUSDT'; // Initially set to BTCUSDT
        let interval = '1m';
        let binanceSocket = null;

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
                        window.location.href = `${coin.toLowerCase()}.php`; // Modify this line with your desired link format
                    });

                    document.getElementById('market-list').appendChild(li);
                } catch (error) {
                    console.error('Error fetching market data:', error);
                }
            }
        }


        // Start real-time WebSocket updates for the selected market symbol
            function startWebSocket(symbol, interval) {
            if (binanceSocket) {
                binanceSocket.close();
            }

            // // Kline WebSocket for candlestick updates
            const socketUrl = `wss://stream.binance.com:9443/ws/${symbol}@kline_${interval}`;
            binanceSocket = new WebSocket(socketUrl);

            

            // Ticker WebSocket for 24-hour change percentage for all markets
            const tickerSocketUrl = `wss://stream.binance.com:9443/ws/${symbol}@ticker`;
            const tickerSocket = new WebSocket(tickerSocketUrl);

            tickerSocket.onmessage = function (event) {
                const tickerData = JSON.parse(event.data);
                const priceChangePercent = parseFloat(tickerData.P); // Use 'P' for the percentage change
                const newPrice = parseFloat(tickerData.c).toFixed(2); // Latest price from ticker data
                const tickerSymbol = tickerData.s.toLowerCase(); // Symbol in lowercase
                
                // Update the header 24h change for BTCUSDT
                if (tickerSymbol === 'bchusdt') {
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

    // Connecting to Binance WebSocket for BCHUSDT order book
    const socket = new WebSocket('wss://stream.binance.com:9443/ws/bchusdt@depth');

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


</script>
</body>
</html>
