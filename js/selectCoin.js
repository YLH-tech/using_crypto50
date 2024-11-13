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

coins.forEach(coin => {
    document.write(coin.id);
});

function changeImage() {
    let img_path = "./assets/images/";
    const image = document.querySelector('.dynamic-image');
    image.src = img_path + coins.id;
}