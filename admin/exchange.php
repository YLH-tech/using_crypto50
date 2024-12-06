<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'get_real_time_prices.php';
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle the coin exchange
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromCoin = $_POST['from_coin'];
    $toCoin = $_POST['to_coin'];
    $amount = floatval($_POST['amount']);


$coinMap = [
    'USDT' => 'USDTUSDT', // USDT to itself for consistency
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

    // Fetch real-time prices
    $prices = getRealTimePricesBinance($coinMap);

    // Get the prices for the selected coins
    $fromCoinPrice = $prices[$coinMap[$fromCoin]]['usd'] ?? 0;
    $toCoinPrice = $prices[$coinMap[$toCoin]]['usd'] ?? 0;

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

        
        $_SESSION['successMsg'] = "Successfully, $amount $fromCoin exchanged to $toCoinAmount $toCoin.";
        header('location:exchangePrice.php');
    } else {
        
        $_SESSION['successMsg'] = "Insufficient balance.";
        header('location:exchangePrice.php');
    }
}


?>