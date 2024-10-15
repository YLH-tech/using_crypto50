    const prices = {
        BTC: { price: "Loading...", percentage: "Loading..." },
        ETH: { price: "Loading...", percentage: "Loading..." },
        LTC: { price: "Loading...", percentage: "Loading..." },
        XRP: { price: "Loading...", percentage: "Loading..." },
        ADA: { price: "Loading...", percentage: "Loading..." },
        DOT: { price: "Loading...", percentage: "Loading..." },
        BNB: { price: "Loading...", percentage: "Loading..." },
        SOL: { price: "Loading...", percentage: "Loading..." },
        DOGE: { price: "Loading...", percentage: "Loading..." },
        UNI: { price: "Loading...", percentage: "Loading..." },
        // BSV: { price: "Loading...", percentage: "Loading..." },
        AVAX: { price: "Loading...", percentage: "Loading..." }
    };

    // Function to update price and percentage in the DOM
    function updatePrice(pair, price, percentage) {
        const priceElements = document.querySelectorAll(`.price`);
        const percentageElements = document.querySelectorAll(`.percentage`);

        priceElements.forEach(element => {
            if (element.previousElementSibling.textContent.includes(pair)) {
                element.textContent = price; // Update the price
                console.log(`Updated price for ${pair}: ${price}`); // Debugging output
            }
        });

        percentageElements.forEach(element => {
            if (element.parentElement.textContent.includes(pair)) {
                // Check if percentage is a valid number
                let formattedPercentage;

                if (isNaN(percentage)) {
                    formattedPercentage = "(-0.00%)"; // Fallback value if NaN
                    element.classList.add('negative'); // Apply negative class for NaN
                } else {
                    formattedPercentage = `(${parseFloat(percentage).toFixed(2)}%)`; // Format percentage with percent sign

                    // Apply color based on the percentage value
                    const percentageValue = parseFloat(percentage);
                    if (percentageValue > 0) {
                        element.classList.remove('negative');
                        element.classList.add('positive');
                    } else {
                        element.classList.remove('positive');
                        element.classList.add('negative');
                    }
                }

                element.textContent = formattedPercentage; // Update the percentage
                console.log(`Updated percentage for ${pair}: ${formattedPercentage}`); // Debugging output
            }
        });
    }


    // Function to create a WebSocket for a given trading pair
    function createWebSocket(pair, retries = 5) {
        const socket = new WebSocket(`wss://stream.binance.com:9443/ws/${pair.toLowerCase()}usdt@trade`);

        socket.onopen = () => {
            console.log(`WebSocket connection established for ${pair}.`);
        };

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            const price = parseFloat(data.p).toFixed(2); // Extract the price and format it

            // Example: Fetching 24h price change
            fetch(`https://api.binance.com/api/v3/ticker/24hr?symbol=${pair.toUpperCase()}USDT`)
                .then(response => response.json())
                .then(data => {
                    const percentage = parseFloat(data.priceChangePercent); // Get percentage change
                    updatePrice(pair.toUpperCase(), price, percentage); // Pass percentage as a number
                })
                .catch(error => {
                    console.error(`Failed to fetch 24h change for ${pair}:`, error);
                    updatePrice(pair.toUpperCase(), price, NaN); // Pass NaN if the fetch fails
                });
        };

        socket.onerror = (error) => {
            console.error(`WebSocket error for ${pair}:`, error);
            // Retry connection if failed
            if (retries > 0) {
                setTimeout(() => {
                    console.log(`Retrying WebSocket for ${pair}, ${retries} attempts left...`);
                    createWebSocket(pair, retries - 1);
                }, 3000); // Retry after 3 seconds
            } else {
                updatePrice(pair.toUpperCase(), "Unavailable", NaN); // Fallback if all retries fail
            }
        };

        socket.onclose = () => {
            console.log(`WebSocket connection closed for ${pair}.`);
        };
    }

    // Create WebSocket connections for each trading pair
    const pairs = ['btc', 'eth', 'ltc', 'xrp', 'ada', 'dot', 'bnb', 'sol', 'doge', 'uni', 'avax'];
    pairs.forEach(pair => {
        createWebSocket(pair);

        // If no price is received within 20 seconds, show "Unavailable"
        setTimeout(() => {
            if (prices[pair.toUpperCase()].price === "Loading...") {
                updatePrice(pair.toUpperCase(), "Unavailable", "Unavailable");
            }
        }, 20000); // 20 seconds timeout
    });
