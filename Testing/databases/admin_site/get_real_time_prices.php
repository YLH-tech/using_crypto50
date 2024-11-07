<?php
// Function to fetch real-time prices from the CoinGecko API
function getRealTimePrices($coinIds) {
    // CoinGecko API URL for the selected coins in USD
    $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=" . implode(',', $coinIds) . "&vs_currencies=usd";
    
    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    // Return decoded JSON as an associative array
    return json_decode($result, true);
}

// Update the coinMap with new coins
$coinMap = [
    'BTC' => 'bitcoin',
    'ETH' => 'ethereum',
    'USDT' => 'tether',
    'USDC' => 'usd-coin',
    'BND' => 'binancecoin',
    'DOGE' => 'dogecoin',
    'TRX' => 'tron',
    'DOT' => 'polkadot',
    'ADA' => 'cardano',
    'BSV' => 'bitcoin-cash-sv',
    'XRP' => 'ripple',
    'LTC' => 'litecoin',
    'EOS' => 'eos',
    'BCH' => 'bitcoin-cash',
    'DF' => 'dfi-money',
    'QTUM' => 'qtum',
    'IOTA' => 'iota',
    'NEO' => 'neo',
    'NAS' => 'nas',
    'ELA' => 'elastos',
    'SNT' => 'status',
    'WICC' => 'wicc'
];

// Fetch prices for all coins
$prices = getRealTimePrices(array_values($coinMap));


// Print prices (for testing purposes)
// print_r($prices);
?>
