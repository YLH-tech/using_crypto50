// TableScript module
const TableScript = (function() {
    const coins = [
        { name: "Bitcoin", symbol: "BTCUSDT", id: "BTC", geckoId: "bitcoin" },
        { name: "Ethereum", symbol: "ETHUSDT", id: "ETH", geckoId: "ethereum" },
        { name: "Dogecoin", symbol: "DOGEUSDT", id: "DOGE", geckoId: "dogecoin" },
        { name: "Polkadot", symbol: "DOTUSDT", id: "DOT", geckoId: "polkadot" },
        { name: "XRP", symbol: "XRPUSDT", id: "XRP", geckoId: "ripple" },
        { name: "Litecoin", symbol: "LTCUSDT", id: "LTC", geckoId: "litecoin" }
    ];

    // Function to create a row for each coin
    function createRow(coin) {
        const row = document.createElement('tr');
        row.id = `row-${coin.id}`;

        row.innerHTML = `
            <td><img src="images/${coin.id}.png" alt="${coin.name} logo" class="coin-logo"> ${coin.name} (${coin.id})</td>
            <td id="price-${coin.id}">--</td>
            <td id="change-${coin.id}">--</td>
            <td id="marketcap-${coin.id}">--</td>
        `;

        row.addEventListener('click', () => {
            window.location.href = `coin-details.html?coin=${coin.id}`;
        });

        document.getElementById('crypto-body').appendChild(row);
    }

    // Fetch market cap using CoinGecko API
    async function fetchMarketCap() {
        const geckoIds = coins.map(coin => coin.geckoId).join(',');
        try {
            const response = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${geckoIds}&vs_currencies=usd&include_market_cap=true`);
            const data = await response.json();

            coins.forEach(coin => {
                const marketCap = data[coin.geckoId]?.usd_market_cap;
                
                // Update market cap
                if (marketCap) {
                    const marketCapInMillions = (marketCap / 1e6).toFixed(2) + 'M';
                    document.getElementById(`marketcap-${coin.id}`).innerHTML = marketCapInMillions;
                }
            });
        } catch (error) {
            console.error('Error fetching market cap data:', error);
        }
    }

    // Function to set up WebSocket for real-time price updates
    function setupWebSocket() {
        const socket = new WebSocket('wss://stream.binance.com:9443/ws');

        // Create the message to subscribe to the relevant coin price updates
        const message = {
            method: "SUBSCRIBE",
            params: coins.map(coin => `${coin.symbol.toLowerCase()}@ticker`),
            id: 1
        };

        socket.onopen = function() {
            socket.send(JSON.stringify(message));
        };

        socket.onmessage = function(event) {
            const data = JSON.parse(event.data);

            // Check if the message contains price updates
            if (data.e === '24hrTicker') {
                const coinId = data.s.replace('USDT', ''); // Extract coin id from symbol
                const price = parseFloat(data.c);
                const changePercent = parseFloat(data.P);

                // Update price and 24h change
                if (coins.find(coin => coin.id === coinId)) {
                    document.getElementById(`price-${coinId}`).innerHTML = `$${price.toFixed(2)}`;
                    document.getElementById(`change-${coinId}`).innerHTML = `${changePercent.toFixed(2)}%`;

                    // Change color based on 24h change
                    const changeElement = document.getElementById(`change-${coinId}`);
                    changeElement.style.color = changePercent >= 0 ? 'green' : 'red';
                }
            }
        };

        socket.onerror = function(error) {
            console.error('WebSocket error:', error);
        };

        socket.onclose = function() {
            console.log('WebSocket connection closed. Reconnecting...');
            setTimeout(setupWebSocket, 5000); // Reconnect after 5 seconds
        };
    }

    // Public methods
    return {
        createRow,
        fetchMarketCap,
        setupWebSocket,
        coins
    };
})();

// UIScript module
const UIScript = (function() {
    const navbar = document.querySelector("[data-navbar]");
    const navbarLinks = document.querySelectorAll("[data-nav-link]");
    const navToggler = document.querySelector("[data-nav-toggler]");

    // Function to toggle the navbar
    function toggleNavbar() {
        navbar.classList.toggle("active");
        navToggler.classList.toggle("active");
        document.body.classList.toggle("active");
    }

    // Function to close the navbar
    function closeNavbar() {
        navbar.classList.remove("active");
        navToggler.classList.remove("active");
        document.body.classList.remove("active");
    }

    // Function to handle header active class on scroll
    function activeHeader() {
        const header = document.querySelector("[data-header]");
        if (window.scrollY > 300) {
            header.classList.add("active");
        } else {
            header.classList.remove("active");
        }
    }

    // Function to handle scroll reveal effect
    function scrollReveal() {
        const sections = document.querySelectorAll("[data-section]");
        sections.forEach(section => {
            if (section.getBoundingClientRect().top < window.innerHeight / 1.5) {
                section.classList.add("active");
            } else {
                section.classList.remove("active");
            }
        });
    }

    // Attach event listeners
    function init() {
        navToggler.addEventListener("click", toggleNavbar);
        navbarLinks.forEach(link => link.addEventListener("click", closeNavbar));
        window.addEventListener("scroll", activeHeader);
        window.addEventListener("scroll", scrollReveal);
    }

    // Public methods
    return {
        init,
        toggleNavbar
    };
})();

// Initialize both scripts after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Table functionality
    TableScript.coins.forEach(coin => TableScript.createRow(coin));
    TableScript.fetchMarketCap();
    TableScript.setupWebSocket(); // Setup WebSocket for real-time prices

    // UI functionality
    UIScript.init();
});
