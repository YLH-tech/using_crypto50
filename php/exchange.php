<?php
session_start();
include 'get_real_time_prices.php'; // Include the function to get real-time prices from CoinGecko
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle the coin exchange
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromCoin = $_POST['from_coin'];
    $toCoin = $_POST['to_coin'];
    $amount = floatval($_POST['amount']);

    // Coin mapping for prices
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

    // Fetch real-time prices
    $prices = getRealTimePrices(array_values($coinMap));

    // Get the prices for the selected coins
    $fromCoinPrice = $prices[$coinMap[$fromCoin]]['usd'];
    $toCoinPrice = $prices[$coinMap[$toCoin]]['usd'];

    // Calculate the USD value of the amount being exchanged
    $fromCoinValueUSD = $amount * $fromCoinPrice;

    // Calculate how much of the 'to' coin the user will receive
    $toCoinAmount = $fromCoinValueUSD / $toCoinPrice;

    // Check user's balance for the 'from' coin
    $sql = "SELECT $fromCoin FROM user_balances WHERE user_id = $userId";
    $balanceResult = $conn->query($sql);
    $balance = $balanceResult->fetch_assoc();

    // Validate if the user has enough balance
    if ($amount > 0 && $balance[$fromCoin] >= $amount) {
        // Update user balance after exchange
        $conn->query("UPDATE user_balances SET $fromCoin = $fromCoin - $amount, $toCoin = $toCoin + $toCoinAmount WHERE user_id = $userId");

        // Record the transaction in the transactions table
        $conn->query("INSERT INTO transactions_exchange (user_id, from_coin, to_coin, from_amount, to_amount, rate) 
                      VALUES ($userId, '$fromCoin', '$toCoin', $amount, $toCoinAmount, $toCoinAmount/$amount)");

        echo "$amount $fromCoin exchanged to $toCoinAmount $toCoin.";
    } else {
        echo "Insufficient balance.";
    }
}


?>