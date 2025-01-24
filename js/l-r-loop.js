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
    AVAX: { price: "Loading...", percentage: "Loading..." }
  };
  
  const pairs = ['BTC', 'ETH', 'LTC', 'XRP', 'ADA', 'DOT', 'BNB', 'SOL', 'DOGE', 'UNI', 'AVAX'];
  
  // Update the DOM with prices and percentage
  function updatePrice(pair, price, percentage) {
    const unavailableText = window.generalTranslations?.unavailable || "Unavailable";
  
    const priceElement = document.querySelector(`.price[data-pair="${pair}"]`);
    const percentageElement = document.querySelector(`.percentage[data-pair="${pair}"]`);
  
    if (priceElement) priceElement.textContent = price || unavailableText;
    if (percentageElement) {
      if (percentage === null) {
        percentageElement.textContent = `(${unavailableText})`;
        percentageElement.className = "percentage negative";
      } else {
        const formattedPercentage = `${parseFloat(percentage).toFixed(2)}%`;
        percentageElement.textContent = `(${formattedPercentage})`;
        percentageElement.className = `percentage ${percentage > 0 ? 'positive' : 'negative'}`;
      }
    }
  }
  
  // Fetch initial 24-hour price changes in batch
  async function fetchInitialData() {
    try {
      // Pass `symbols` as a properly formatted JSON array
      const symbols = JSON.stringify(pairs.map(pair => `${pair}USDT`));
      const response = await fetch(`https://api.binance.com/api/v3/ticker/24hr?symbols=${encodeURIComponent(symbols)}`);
      const data = await response.json();
  
      data.forEach(item => {
        const pair = item.symbol.replace('USDT', '');
        const price = parseFloat(item.lastPrice).toFixed(2);
        const percentage = parseFloat(item.priceChangePercent);
        updatePrice(pair, `$${price}`, percentage);
      });
    } catch (error) {
      // Handle fetch errors
      pairs.forEach(pair => updatePrice(pair, "Unavailable", null));
    }
  }
  
  
  // WebSocket connection for real-time updates
  function setupWebSocket() {
    const streams = pairs.map(pair => `${pair.toLowerCase()}usdt@trade`).join('/');
    const socket = new WebSocket(`wss://stream.binance.com:9443/stream?streams=${streams}`);
  
    socket.onmessage = (event) => {
      const message = JSON.parse(event.data);
      const { s: symbol, p: price } = message.data;
      const pair = symbol.replace('USDT', '');
      updatePrice(pair, `$${parseFloat(price).toFixed(2)}`, null); // Update price in real-time
    };
  
    socket.onerror = () => {
      console.error("WebSocket error. Reconnecting in 5 seconds...");
      setTimeout(setupWebSocket, 5000); // Reconnect after 5 seconds
    };
  
    socket.onclose = () => {
      console.warn("WebSocket closed. Reconnecting...");
      setupWebSocket();
    };
  
    // Cleanup on page unload
    window.addEventListener("beforeunload", () => socket.close());
  }
  
  // Initialize
  fetchInitialData();
  setupWebSocket();
  