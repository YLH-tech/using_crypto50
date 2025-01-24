<?php
// Function to fetch real-time prices from the Binance API
function getRealTimePricesBinance($coinSymbols) {
    // Binance API URL for all tickers
    $apiUrl = "https://api.binance.com/api/v3/ticker/price";

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response
    $prices = json_decode($result, true);

    // Map Binance prices to requested coins
    $mappedPrices = [];
    foreach ($coinSymbols as $coin => $binanceSymbol) {
        foreach ($prices as $priceData) {
            if ($priceData['symbol'] === $binanceSymbol) {
                $mappedPrices[$coin] = ['usd' => (float)$priceData['price']];
                break;
            }
        }
    }

    return $mappedPrices;// USD PRICE
}

// Map coins to Binance trading pairs
$coinMap = [
    'USDT' => 'USDT', // USDT to itself for consistency
    'BTC' => 'BTCUSDT',
    'ETH' => 'ETHUSDT',
    'USDC' => 'USDCUSDT',
    'BNB' => 'BNBUSDT',
    'XRP' => 'XRPUSDT',
    'DOGE' => 'DOGEUSDT',
    'SOL' => 'SOLUSDT',
    'ADA' => 'ADAUSDT',
    'TRX' => 'TRXUSDT',
    'DOT' => 'DOTUSDT',
    'LTC' => 'LTCUSDT',
    'BCH' => 'BCHUSDT',
    'ETC' => 'ETCUSDT',
    'UNI' => 'UNIUSDT',
    'LINK' => 'LINKUSDT',
    'AVAX' => 'AVAXUSDT',
    'NEO' => 'NEOUSDT',
    'EOS' => 'EOSUSDT',
    'ARB' => 'ARBUSDT',
    'APT' => 'APTUSDT',
    'TON' => 'TONUSDT'
];

// Fetch prices for all specified coins
$prices = getRealTimePricesBinance($coinMap);

// Assign prices directly for USD estimation in the dashboard
$usdtPrice = 1;
$btcPrice = $prices['BTC']['usd'] ?? 0;
$ethPrice = $prices['ETH']['usd'] ?? 0;
$usdcPrice = $prices['USDC']['usd'] ?? 0;
$bnbPrice = $prices['BNB']['usd'] ?? 0;
$xrpPrice = $prices['XRP']['usd'] ?? 0;
$dogePrice = $prices['DOGE']['usd'] ?? 0;
$solPrice = $prices['SOL']['usd'] ?? 0;
$adaPrice = $prices['ADA']['usd'] ?? 0;
$trxPrice = $prices['TRX']['usd'] ?? 0;
$dotPrice = $prices['DOT']['usd'] ?? 0;
$ltcPrice = $prices['LTC']['usd'] ?? 0;
$bchPrice = $prices['BCH']['usd'] ?? 0;
$etcPrice = $prices['ETC']['usd'] ?? 0;
$uniPrice = $prices['UNI']['usd'] ?? 0;
$linkPrice = $prices['LINK']['usd'] ?? 0;
$avaxPrice = $prices['AVAX']['usd'] ?? 0;
$neoPrice = $prices['NEO']['usd'] ?? 0;
$eosPrice = $prices['EOS']['usd'] ?? 0;
$arbPrice = $prices['ARB']['usd'] ?? 0;
$aptPrice = $prices['APT']['usd'] ?? 0;
$tonPrice = $prices['TON']['usd'] ?? 0;

// Print prices for testing
// print_r($prices);
?>
