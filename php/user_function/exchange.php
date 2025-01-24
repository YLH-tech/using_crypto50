<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user balances
    $sql = "SELECT USDT, BTC, ETH, USDC, BNB, XRP, DOGE, SOL, ADA, TRX, DOT, LTC, BCH, ETC, UNI, LINK, AVAX, NEO, EOS, ARB, APT, TON 
            FROM user_balances WHERE user_id = ?";
    $balanceStmt = $conn->prepare($sql);
    $balanceStmt->bind_param("i", $userId);
    $balanceStmt->execute();
    $balanceResult = $balanceStmt->get_result();
    $balance = $balanceResult->fetch_assoc();

// Process the form if submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['clear_record'])) {
        $amount = $_POST['amount'];
        $fromCoin = $_POST['from_coin'];
        $toCoin = $_POST['to_coin'];

    // Check balance and amount
    if ($amount <= 0 || $amount > $balance[$fromCoin]) {
        $_SESSION['errorMsg'] = "Insufficient balance.";
            header('Location: exchange.php');
        exit();
    }

    // Calculate received amount based on exchange rate
    $exchangeRate = $_POST['exchange_rate'];
    $receivedAmount = $amount * $exchangeRate;

    // Update balances in the database
    $updateStmt = $conn->prepare("UPDATE user_balances 
                                  SET $fromCoin = $fromCoin - ?, $toCoin = $toCoin + ? 
                                  WHERE user_id = ?");
    $updateStmt->bind_param("ddi", $amount, $receivedAmount, $userId);
    $updateStmt->execute();

    // Insert transaction into the database
    $transactionStmt = $conn->prepare("INSERT INTO transactions_exchange (user_id, from_coin, to_coin, from_amount, to_amount, rate) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
    $transactionStmt->bind_param("issdds", $userId, $fromCoin, $toCoin, $amount, $receivedAmount, $exchangeRate);
    $transactionStmt->execute();

    // Set session message with placeholders
    $_SESSION['errorMsg'] = "Your Exchange is successful!";
    header('Location: exchange.php');
    exit();
}

// Fetch user's exchange records
$recordsSql = "SELECT from_coin, to_coin, from_amount, to_amount, rate, transaction_date 
               FROM transactions_exchange WHERE user_id = $userId ORDER BY transaction_date DESC";
$recordsResult = $conn->query($recordsSql);

// Handle record clearing
if (isset($_POST['clear_record'])) {
    $deleteStmt = $conn->prepare("DELETE FROM transactions_exchange WHERE user_id = ?");
    $deleteStmt->bind_param("i", $userId);
    $deleteStmt->execute();
    header('Location: exchange.php');
    exit();
}

// Translation function
function translate($key, $placeholders = []) {
    global $translations;
    // Default to English if no translation is found
    $message = $translations[$key] ?? $key;

    // Replace placeholders dynamically
    foreach ($placeholders as $placeholder => $value) {
        $message = str_replace("{" . $placeholder . "}", $value, $message);
    }

    return $message;
}

// Load translations based on user's selected language
$selectedLanguage = $_SESSION['language'] ?? 'en';
$translations = json_decode(file_get_contents("../../translations/$selectedLanguage.json"), true);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="exchange_coins">Coin Exchange</title>
    <link id="themeStylesheet" rel="stylesheet" href="./light-mode.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../js/ex_translate.js"></script>

</head>

<body>
    <!-- night mode script -->
    <script src="./darkMode.js"></script>

    <!-- Navigation Bar -->
    <nav>
        <!-- Left-side menus -->
        <div class="nav-subdiv">

            <!-- Web Logo -->
            <a href="../../index.php" class="flex items-center">
                <img src="../../assets/images/LOGO.png" alt="LOGO" class="web-logo w-8 mx-3">
                <h1 class="text-2xl font-bold" data-translate="nav_logo"> Bithumbnn</h1>
            </a>

            <div id="non-mobile" hidden>
                <div class="dropdown">
                    <button class="dropbtn">
                        <span data-translate="nav_buy_crypto">Buy Crypto</span> <i class="fa-solid fa-caret-down drop-down-arrow"></i>
                    </button>
                    <div class="dropdown-content">

                        <!-- Deposit -->
                        <a href="./depo_adding.html"><img src="../../assets/images/deposit.png" alt="Deposit">
                            <span>
                                <h5 data-translate="nav_deposit">Deposit</h5>
                                <p data-translate="nav_deposit_desc">Crypto deposit and your records</p>
                            </span>
                        </a>

                        <!-- Withdraw -->
                        <a href="./adding-withdraw.html"><img src="../../assets/images/withdraw.png" alt="Withdraw">
                            <span>
                                <h5 data-translate="nav_withdraw">Withdraw</h5>
                                <p data-translate="nav_withdraw_desc">Crypto withdraw and your records</p>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Markets -->
                <div class="dropdown">
                    <button class="dropbtn">
                        <span data-translate="nav_markets">Markets</span> <i class="fa-solid fa-caret-down drop-down-arrow"></i>
                    </button>
                    <div class="dropdown-content">

                        <!-- Market Data -->
                        <a href="../../marketData.php"><img src="../../assets/images/web-analytics.png" alt="Market Data">
                            <span>
                                <h5 data-translate="nav_market_data">Market Data</h5>
                                <p data-translate="nav_market_data_desc">Capture market opportunities</p>
                            </span>
                        </a>

                        <!-- Feed -->
                        <a href="../../cryptoNews.php"><img src="../../assets/images/feed.png" alt="Feed">
                            <span>
                                <h5 data-translate="nav_feed">Feed</h5>
                                <p data-translate="nav_feed_desc">Discover current trends</p>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Exchange -->
                <div class="dropdown">
                    <button class="dropbtn">
                        <span data-translate="nav_exchange">Exchange</span> <i class="fa-solid fa-caret-down drop-down-arrow"></i>
                    </button>
                    <div class="dropdown-content">
                        <!-- User Balance Profile -->
                        <a href="./user_balance.php"><img src="../../assets/images/tax-credit.png" alt="User Balance Profile Pic">
                            <span>
                                <h5 data-translate="nav_user_profile">User Balance Profile</h5>
                                <p data-translate="nav_user_profile_desc">Your balance and your transaction history</p>
                            </span>
                        </a>

                        <!-- Coin Exchange -->
                        <a href="./exchange.php"><img src="../../assets/images/exchange (1).png" alt="Spot Trading">
                            <span>
                                <h5 data-translate="nav_coin_exchange">Coin Exchange</h5>
                                <p data-translate="nav_coin_exchange_desc">Easily trade with any crypto combination</p>
                            </span>
                        </a>

                        <!-- Trend Trading -->
                        <a href="../trade_order/btcusdt.php"><img src="../../assets/images/technical-analysis.png" alt="Margin Trading">
                            <span>
                                <h5 data-translate="nav_trend_trading">Trend Trading</h5>
                                <p data-translate="nav_trend_trading_desc">The most popular trading nowadays</p>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- More -->
                <div class="dropdown">
                    <button class="dropbtn">
                        <span data-translate="nav_more">More</span> <i class="fa-solid fa-caret-down drop-down-arrow"></i>
                    </button>
                    <div class="dropdown-content">

                        <!-- Help Center -->
                        <a href="../../helpCenter.php"><img src="../../assets/images/help.png" alt="Help Center Pic">
                            <span>
                                <h5 data-translate="nav_help_center">Help Center</h5>
                                <p data-translate="nav_help_center_desc">Ready to help you</p>
                            </span>
                        </a>

                        <!-- Records History -->
                        <a href="../../recordsHistory.php"><img src="../../assets/images/clock.png" alt="History Pic">
                            <span>
                                <h5 data-translate="nav_records_history">Records History</h5>
                                <p data-translate="nav_records_history_desc">Footprints are here</p>
                            </span>
                        </a>

                        <!-- Customer Service -->
                        <a href="#"><img src="../../assets/images/insurance-agent.png" alt="Customer Service">
                            <span>
                                <h5 data-translate="nav_customer_service">Customer Service</h5>
                                <p data-translate="nav_customer_service_desc">Ready to help you</p>
                            </span>
                        </a>
                    </div>
                </div>
            </div>



        </div>

        <!-- Right-side menu -->
        <div class="nav-subdiv">
            <?php
            if (!isset($_SESSION["user_id"])) {
            ?>
                <div class="unregisted">
                    <a href="../../login.php" data-translate="nav_login">Log In</a>
                    <a href="../../signup.php" data-translate="nav_signup"><span class="signup-btn">Sign Up</span></a>
                </div>
            <?php
            } else {
            ?>
                <a href="../../profile.php"><img src="../../assets/profile/default_pfp.png" alt="profile"
                        class="w-12 inline-block"></a>

            <?php
            }
            ?>
            <div class="clickdown">
                <button onclick="clickdown()"><i class="fa-solid fa-bars text-3xl align-middle clickbtn"></i></button>
                <div id="myDropdown" class="clickdown-content">
                    <!-- Menu Options -->
                    <ul class="w-[90%] m-auto mobile">
                        <li>
                            <a href="./depo_adding.html">
                                <img src="../../assets/images/deposit.png" alt="deposit logo" class="w-8">
                                <span data-translate="nav_deposit">Deposit</span>
                            </a>
                        </li>
                        <li>
                            <a href="./adding-withdraw.html">
                                <img src="../../assets/images/withdraw.png" alt="withdraw logo" class="w-8">
                                <span data-translate="nav_withdraw">Withdraw</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../marketData.php">
                                <img src="../../assets/images/market.png" alt="market logo" class="w-8">
                                <span data-translate="nav_market_data">Market Data</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../cryptoNews.php">
                                <img src="../../assets/images/feed.png" alt="Feed logo" class="w-8">
                                <span data-translate="nav_feed">Feed</span>
                            </a>
                        </li>
                        <li>
                            <a href="./user_balance.php">
                                <img src="../../assets/images/tax-credit.png" alt="User Balance Profile logo" class="w-8">
                                <span data-translate="nav_user_profile">User Balance Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="./exchange.php">
                                <img src="../../assets/images/exchange.png" alt="Coin Exchange logo" class="w-8">
                                <span data-translate="nav_coin_exchange">Coin Exchange</span>
                            </a>
                        </li>
                        <li>
                            <a href="../trade_order/btcusdt.php">
                                <img src="../../assets/images/technical-analysis.png" alt="Transfer" class="w-8">
                                <span data-translate="nav_trend_trading">Trend Trading</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../helpCenter.php">
                                <img src="../../assets/images/help.png" alt="help logo" class="w-8">
                                <span data-translate="nav_help_center">Help Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../recordsHistory.php">
                                <img src="../../assets/images/clock.png" alt="clock logo" class="w-8">
                                <span data-translate="nav_records_history">Records History</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="../../assets/images/insurance-agent.png" alt="Customer Service logo" class="w-8">
                                <span data-translate="nav_customer_service">Customer Service</span>
                            </a>
                        </li>
                    </ul>


                    <?php
                    if (isset($_SESSION['user_id'])) {
                    ?>
                        <a href="../trade_order/btcusdt.php" class="w-[100%] bg-[#78B7D0] text-center rounded-md" data-translate="nav_trade_now">Trade
                            Now</a>
                    <?php
                    } else {
                    ?>
                        <div class="click-regist">
                            <a href="../../login.php" class="text-2xl" data-translate="nav_login">Log In</a>
                            <a href="../../signup.php"><span class="signup-btn text-2xl" data-translate="nav_signup">Sign Up</span></a>
                        </div>
                    <?php
                    }
                    ?>

                    <!-- Setting Options -->
                    <ul class="w-[90%] m-auto">
                        <li class="cursor-pointer">
                            <h5>
                                <i class="fa-solid fa-language mr-3 text-2xl"></i>
                                <span data-translate="nav_language">Language:</span>
                                <select id="language-selector" onchange="changeLanguage(this.value)" style="background-color: grey;">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="zh">Chinese</option>
                                    <option value="fr">French</option>
                                    <option value="ja">Japanese</option>
                                </select>
                            </h5>
                        </li>
                        <li class="flex items-center justify-between cursor-pointer">
                            <h5>
                                <i class="fa-solid fa-circle-half-stroke mr-3 text-2xl"></i>
                                <span data-translate="nav_dark_mode">Dark Mode</span>
                            </h5>
                            <button id="dark-switch"><i class="fa-solid fa-moon text-3xl" id="mode-btn"></i></button>
                        </li>
                        <li class="cursor-pointer" id="download-app">
                            <h5>
                                <i class="fa-regular fa-circle-down mr-3 text-2xl"></i>
                                <span data-translate="nav_download_app">Download App</span>
                            </h5>
                        </li>
                        <script>
                            document.getElementById('download-app').addEventListener('click', function() {
                                window.location.href = '../../download.html';
                            });
                        </script>

                        <?php if (isset($_SESSION['user_id'])) { ?>
                            <a href="./php/user_function/logout.php" class="cursor-pointer">
                                <h5 class="text-red-500">
                                    <i class="fa-solid fa-right-from-bracket mr-3 text-2xl"></i>
                                    <span data-translate="nav_logout">Log Out</span>
                                </h5>
                            </a>
                        <?php } ?>

                    </ul>

                </div>
            </div>
        </div>
    </nav>

    <br><br><br>

    <h2 class="text-center text-4xl" data-translate="exchange_coins">Exchange Coins</h2>
    <br>
    <p class="text-center sec-font-color" data-translate="swap_assets">Swap assets effortlessly and securely with CoinEX's self-developed algorithm
    </p>
    <br>

    <!-- Main Section -->
    <main class="main-section">
        <!-- Exchange section -->
        <div class="main-container">
        <?php if (isset($_SESSION['errorMsg'])): ?>
            <div class="error" data-translate="<?= $_SESSION['errorMsg']; ?>">
                <?php unset($_SESSION['errorMsg']); ?>
            </div>
        <?php endif; ?>
            <form method="POST" action="#">
                <!-- Other form fields -->
                <input type="hidden" name="exchange_rate" id="exchange_rate">
                <div class="horizontal-container">
                    <div>
                        <h1 data-translate="exchange_rate">Exchange Rate</h1>
                        <span id="exchange-rate">Waiting for data...</span>
                    </div>
                    <div>
                        <h1 data-translate="balance">Balance</h1>
                        <span id="balance"></span>
                    </div>
                    <div>
                        <h1 data-translate="available_amount">Available Amount for Coins</h1>
                        <span id="available-amount"></span>
                    </div>
                </div>
                <br><br>
                <!-- From coin -->
                <div class="from-coin-div">
                    <label data-translate="from_coin">From Coin:</label>
                    <select name="from_coin" id="from_coin" required onchange="updateCoinInfo()">
                        <option value="BTC">BTC</option>
                        <option value="ETH">ETH</option>
                        <option value="BNB">BNB</option>
                        <option value="USDC">USDC</option>
                        <option value="XRP">XRP</option>
                        <option value="DOGE">DOGE</option>
                        <option value="SOL">SOL</option>
                        <option value="ADA">ADA</option>
                        <option value="TRX">TRX</option>
                        <option value="DOT">DOT</option>
                        <option value="LTC">LTC</option>
                        <option value="BCH">BCH</option>
                        <option value="ETC">ETC</option>
                        <option value="UNI">UNI</option>
                        <option value="LINK">LINK</option>
                        <option value="AVAX">AVAX</option>
                        <option value="NEO">NEO</option>
                        <option value="EOS">EOS</option>
                        <option value="ARB">ARB</option>
                        <option value="APT">APT</option>
                        <option value="TON">TON</option>
                    </select>
                    <br><br>
                    <!-- Amount input with Max button -->
                    <div class="amount-input gap-5">
                        <input type="number" class="w-[100%] h-10 p-5" step="0.000001" name="amount" id="amountInput"
                            placeholder="Enter amount" data-translate="enter_amount">
                        <button type="button" onclick="setMaxAmount()" data-translate="max">Max</button>
                    </div>
                </div>
                <br>
                <!-- Switch icon -->
                <img src="../../assets/images/transfer.png" alt="Exchange icon" class="w-10 m-auto cursor-pointer"
                    onclick="swapCoins()">
                <br>
                <!-- To coin -->
                <div class="to-coin-div">
                    <label data-translate="to_coin">To Coin:</label>
                    <select name="to_coin" id="to_coin" required onchange="updateCoinInfo()">
                        <option value="USDT">USDT</option>
                    </select>
                </div>
                <br>
                <button type="submit" class="w-[100%] h-12 bg-green-400 rounded-[50px]" data-translate="exchange">Exchange</button>
            </form>


        </div>

        <!-- Your Balance -->
        <div class="coin-price">
            <h2 class="text-4xl" data-translate="your_balance">Current Balances</h2>
            <br>
            <!-- Table for coin price -->
            <table class="coin-table">
                <thead>
                    <tr>
                        <th class="text-left text-2xl" data-translate="coin">Coin</th>
                        <th class="text-right text-2xl" data-translate="price">Price</th>
                    </tr>
                </thead>
                <tbody class="scrollable-area">
                    <tr>
                        <td class="coins"><img src="../../images/BTC.png" alt="BTC icon"> BTC: </td>
                        <td class="coin-prices" id="BTC_balance">
                            <?php echo $balance['BTC']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/ETH.png" alt="ETH icon"> ETH: </td>
                        <td class="coin-prices" id="ETH_balance">
                            <?php echo $balance['ETH']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/USDT.png" alt="USDT icon"> USDT: </td>
                        <td class="coin-prices" id="USDT_balance">
                            <?php echo $balance['USDT']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/USDC.png" alt="USDC icon"> USDC: </td>
                        <td class="coin-prices" id="USDC_balance">
                            <?php echo $balance['USDC']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/BNB.png" alt="BNB icon"> BNB: </td>
                        <td class="coin-prices" id="BNB_balance">
                            <?php echo $balance['BNB']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/DOGE.png" alt="DOGE icon"> DOGE: </td>
                        <td class="coin-prices" id="DOGE_balance">
                            <?php echo $balance['DOGE']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/TRX.png" alt="TRX icon"> TRX: </td>
                        <td class="coin-prices" id="TRX_balance">
                            <?php echo $balance['TRX']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/DOT.png" alt="DOT icon"> DOT: </td>
                        <td class="coin-prices" id="DOT_balance">
                            <?php echo $balance['DOT']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/ADA.png" alt="ADA icon"> ADA: </td>
                        <td class="coin-prices" id="ADA_balance">
                            <?php echo $balance['ADA']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/BCH.png" alt="BCH icon"> BCH: </td>
                        <td class="coin-prices" id="BCH_balance">
                            <?php echo $balance['BCH']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/XRP.png" alt="XRP icon"> XRP: </td>
                        <td class="coin-prices" id="XRP_balance">
                            <?php echo $balance['XRP']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/LTC.png" alt="LTC icon"> LTC: </td>
                        <td class="coin-prices" id="LTC_balance">
                            <?php echo $balance['LTC']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/EOS.png" alt="EOS icon"> EOS: </td>
                        <td class="coin-prices" id="EOS_balance">
                            <?php echo $balance['EOS']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/APT.png" alt="APT icon"> APT: </td>
                        <td class="coin-prices" id="APT_balance">
                            <?php echo $balance['APT']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/ARB.png" alt="ARB icon"> ARB: </td>
                        <td class="coin-prices" id="ARB_balance">
                            <?php echo $balance['ARB']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/AVAX.png" alt="AVA icon"> AVA: </td>
                        <td class="coin-prices" id="AVAX_balance">
                            <?php echo $balance['AVAX']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/ETC.png" alt="ETC icon"> ETC: </td>
                        <td class="coin-prices" id="ETC_balance">
                            <?php echo $balance['ETC']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/NEO.png" alt="NEO icon"> NEO: </td>
                        <td class="coin-prices" id="NEO_balance">
                            <?php echo $balance['NEO']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/LINK.png" alt="LINK icon"> LINK: </td>
                        <td class="coin-prices" id="LINK_balance">
                            <?php echo $balance['LINK']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/SOL.png" alt="SOL icon"> SOL: </td>
                        <td class="coin-prices" id="SOL_balance">
                            <?php echo $balance['SOL']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/TON.png" alt="TON icon"> TON: </td>
                        <td class="coin-prices" id="TON_balance">
                            <?php echo $balance['TON']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="coins"><img src="../../images/UNI.png" alt="UNI icon"> UNI: </td>
                        <td class="coin-prices" id="UNI_balance">
                            <?php echo $balance['UNI']; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
    <br>
    <br>
    <!-- Exchange History -->
    <div class="exchange-history">
        <h2 class="text-2xl" data-translate="your_exchange_records">Your Exchange Records</h2>
        <br>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md " name="clear_record"
                onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                Record</button>
        </form><br>
        <div class="horizontal-scroll">

            <table>
                <thead>
                    <tr>
                        <th class="rounded-l-xl" data-translate="from_coin">From Coin</th>
                        <th data-translate="to_coin">To Coin</th>
                        <th data-translate="From Amount">From Amount</th>
                        <th data-translate="To Amount">To Amount</th>
                        <th data-translate="price">Price</th>
                        <th class="rounded-r-xl" data-translate="Exchanged At">Exchanged At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recordsResult->num_rows > 0): ?>
                        <?php while ($record = $recordsResult->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo $record['from_coin']; ?>
                                </td>
                                <td>
                                    <?php echo $record['to_coin']; ?>
                                </td>
                                <td>
                                    <?php echo $record['from_amount']; ?>
                                </td>
                                <td>
                                    <?php echo $record['to_amount']; ?>
                                </td>
                                <td>
                                    <?php echo $record['rate']; ?>
                                </td>
                                <td>
                                    <?php echo $record['transaction_date']; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" data-translate="no_exchange_records">No exchange records found.</td>
                        </tr>
                    <?php endif; ?>


                </tbody>
            </table>
        </div>
    </div>
    <br><br>
    <!-- Footer -->
    <footer>
        <div class="align-margin footer-sec"> <!-- to align margin -->
            <!-- Contact Icon Section -->
            <div class="contact-container w-[30%] mb-5">
                <span>
                    <div><img src="./assets/images/LOGO.png" alt="LOGO" class="web-logo">
                        <h1 class="text-2xl font-bold" data-translate="footer_logo_text">Bithumbnn</h1>
                    </div>
                    <p data-translate="footer_slogan">Making Crypto Trading Easier</p>
                </span>

                <!-- Icon Container -->
                <span class="icon-container">
                    <!-- Telegram Account -->
                    <a href="#"><img src="./assets/images/telegram.png" alt="Telegram" class="contact-icons"></a>
                    <!-- Email -->
                    <a href="#"><img src="./assets/images/gmail.png" alt="Email" class="contact-icons"></a>
                    <p data-translate="footer_copyright">© 2024-2025 Bithumbnn.com. All rights reserved.</p>
                </span>
            </div>

            <!-- About -->
            <ul>
                <span class="gen-info-container" onclick="myFunction_gsm('sec1')">
                    <h2 class="text-2xl font-bold mb-5" data-translate="footer_about">About</h2><i class="fa-solid fa-angle-right" id="right-arrow1"></i>
                </span>
                <span id="sec1">
                    <li><a href="./helpCenter.php" data-translate="footer_about_us">About us</a></li>
                    <li><a href="#" data-translate="footer_contact_us">Contact Us</a></li>
                    <li><a href="./termAgreement.php" data-translate="footer_terms_policy">Terms and policy</a></li>
                </span>
            </ul>

            <!-- Services -->
            <ul>
                <span class="gen-info-container" onclick="myFunction_gsm('sec2')">
                    <h2 class="text-2xl font-bold mb-5" data-translate="footer_services">Services</h2>
                    <i class="fa-solid fa-angle-right" id="right-arrow2"></i>
                </span>
                <!-- First Column -->
                <span id="sec2">
                    <li data-translate="footer_future_trading">Future Trading</li>
                    <li data-translate="footer_global_market">Global Market</li>
                    <li data-translate="footer_top_gainers">Top Gainers</li>
                    <li data-translate="footer_top_losers">Top Losers</li>
                    <li data-translate="footer_coin_exchange">Coin Exchange</li>
                </span>
            </ul>

            <!-- Support -->
            <ul>
                <span class="gen-info-container" onclick="myFunction_gsm('sec3')">
                    <h2 class="text-2xl font-bold mb-5" data-translate="footer_support">Support</h2>
                    <i class="fa-solid fa-angle-right" id="right-arrow3"></i>
                </span>
                <span id="sec3">
                    <li><a href="./helpCenter.php" data-translate="footer_help_center">Help Center</a></li>
                    <li data-translate="footer_secure_security">Secure Security</li>
                    <li data-translate="footer_balance">Balance</li>
                    <li data-translate="footer_official_version">Official Version</li>
                </span>
            </ul>
        </div>
        <!-- Footer baseline (Second Container) -->
        <div class="footer-baseline">
            <span class="align-margin flex justify-between leading-[50px] text-white">
                <h3 data-translate="footer_version">Version: 1.0</h3>
            </span>
        </div>
    </footer>

    <!-- Side menu icon clicking -->
    <script src="../../js/dropdownScript.js"></script>

    <!-- Clicking buttons for more infos in footer -->
    <script>
        function myFunction_gsm(sectionId) {
            let sec = document.querySelectorAll('.sec');

            sec.forEach(itm => {
                if (itm.id !== sectionId) itm.style.display = 'none'
            })
            var x = document.getElementById(sectionId);
            let arrow_no;
            if (sectionId == 'sec1') {
                arrow_no = 1;
            } else if (sectionId == 'sec2') {
                arrow_no = 2;
            } else {
                arrow_no = 3;
            }
            let right_arrow = document.getElementById(`right-arrow${arrow_no}`);


            if (x.style.display === "block") {
                x.style.display = "none";
                right_arrow.style.rotate = "0deg";
            } else {
                x.style.display = "block";
                right_arrow.style.rotate = "90deg";
            }
        }

        let sec = document.querySelectorAll('.sec');
        sec.forEach(itm => {
            itm.style.display = 'none'
        })
    </script>

    <!-- This is for 'max' button beside input filed -->
    <script>
        // Function to set the maximum available balance in the input field
        function setMaxAmount() {
            // Get the selected "fromCoin" and the corresponding available balance
            const fromCoin = document.getElementById('from_coin').value;
            const maxAmount = balanceData[fromCoin]; // Get the balance for the selected "fromCoin"

            if (maxAmount) {
                // Set the input field to the max available balance
                document.getElementById('amountInput').value = maxAmount;
                updateAvailableAmount(); // Update the available amount text immediately
            } else {
                console.log("Error: No balance available for the selected coin.");
            }
        }
    </script>



    <script>
        // Pass the balance data from PHP to JavaScript
        let balanceData = <?php echo json_encode($balance); ?>;
    </script>

    <script>
        let socket;
        let fromCoin = "BTC"; // Default from coin
        let toCoin = "USDT"; // Default to coin
        let currentExchangeRate = 0; // Store the current exchange rate

        async function connectWebSocket() {
            // Construct the symbol dynamically based on current selected coins (handle swap correctly)
            const symbol = fromCoin.toLowerCase() + toCoin.toLowerCase(); // Before swap (fromCoin -> toCoin)

            // If swapped, the symbol should be reversed
            const swappedSymbol = toCoin.toLowerCase() + fromCoin.toLowerCase(); // After swap (toCoin -> fromCoin)

            // Use the swappedSymbol only if a swap has occurred
            const pairSymbol = (fromCoin === 'USDT') ? swappedSymbol : symbol;

            // Set the initial "Waiting for data..." message
            const waitingMessage = await getTranslatedText('waiting'); // Retrieve translated text for 'Waiting for data...'
            document.getElementById('exchange-rate').textContent = waitingMessage;
            document.getElementById('balance').textContent = waitingMessage;
            document.getElementById('available-amount').textContent = waitingMessage;
            if (socket) {
                socket.close(); // Close the previous WebSocket connection if it exists
            }

            socket = new WebSocket(`wss://stream.binance.com:9443/ws/${pairSymbol}@trade`);

            socket.onmessage = function(event) {
                const data = JSON.parse(event.data);
                const price = parseFloat(data.p).toFixed(6); // Price of the coin pair

                // After swap, reverse the rate (1 USDT = 1 / price {fromCoin})
                if (fromCoin === 'USDT') {
                    // When USDT is involved, display the rate in reverse
                    const reversedPrice = (1 / price).toFixed(6); // Inverse of the price
                    document.getElementById('exchange-rate').textContent = `1 USDT = ${reversedPrice} ${toCoin}`;

                    // Update balance display for the selected 'fromCoin'
                    document.getElementById('balance').textContent = `${balanceData[fromCoin]} ${fromCoin}`;

                    currentExchangeRate = 1 / price; // Store the exchange rate
                } else {
                    // Normal case: 1 BTC = x.xxxxxx USDT
                    document.getElementById('exchange-rate').textContent = `1 ${fromCoin} = ${price} ${toCoin}`;

                    // Update balance display for the selected 'fromCoin'
                    document.getElementById('balance').textContent = `${balanceData[fromCoin]} ${fromCoin}`;

                    currentExchangeRate = price; // Store the exchange rate
                }

                // Update the available amount when the exchange rate changes
                updateAvailableAmount();
            };

            socket.onopen = function() {
                console.log("WebSocket connected.");
            };

            socket.onclose = function() {
                console.log("WebSocket connection closed.");
            };

            socket.onerror = function(error) {
                console.log("WebSocket error: ", error);
                document.getElementById('exchange-rate').textContent = 'Failed to fetch data.';
            };
        }

        // Call the function to connect to the WebSocket on page load
        window.onload = function() {
            connectWebSocket(); // Establish WebSocket for real-time updates
        };

        // Function to update the coin pair info (WebSocket connection)
        function updateCoinInfo() {
            fromCoin = document.getElementById('from_coin').value;
            toCoin = document.getElementById('to_coin').value;

            // Reconnect WebSocket for the new pair
            connectWebSocket();
        }

        // Function to calculate the available amount based on input amount and exchange rate
        function updateAvailableAmount() {
            const amountInput = document.getElementById('amountInput').value;
            if (amountInput && !isNaN(amountInput) && currentExchangeRate > 0) {
                // Calculate available amount
                const availableAmount = (amountInput * currentExchangeRate).toFixed(6);
                document.getElementById('available-amount').textContent = `${availableAmount} ${toCoin}`;
            } else {
                document.getElementById('available-amount').textContent = `0.000000 ${toCoin}`;
            }
        }

        // Function to update the available amount when the input field changes
        document.getElementById('amountInput').addEventListener('input', updateAvailableAmount);

        // Function to swap the coins
        function swapCoins() {
            // Get the dropdown elements
            const fromCoinDropdown = document.getElementById('from_coin');
            const toCoinDropdown = document.getElementById('to_coin');

            // Get their current values
            const fromCoinValue = fromCoinDropdown.value;
            const toCoinValue = toCoinDropdown.value;

            // Swap the values in the dropdowns
            fromCoinDropdown.value = toCoinValue;
            toCoinDropdown.value = fromCoinValue;

            // Optionally, you can update the dropdown HTML to ensure the coin options are correct.
            const fromCoinOptions = fromCoinDropdown.innerHTML;
            const toCoinOptions = toCoinDropdown.innerHTML;

            fromCoinDropdown.innerHTML = toCoinOptions;
            toCoinDropdown.innerHTML = fromCoinOptions;

            // Clear the input field when the coin is swapped
            document.getElementById('amountInput').value = '';

            // Update the 'fromCoin' and 'toCoin' variables after the swap
            fromCoin = fromCoinDropdown.value;
            toCoin = toCoinDropdown.value;

            // Update the exchange rate and WebSocket data after swapping coins
            updateCoinInfo(); // Function to update exchange rate and WebSocket
        }



        // Function to set the exchange rate to the hidden input field before form submission
        function updateExchangeRate() {
            const exchangeRateInput = document.getElementById('exchange_rate');
            exchangeRateInput.value = currentExchangeRate; // Use the latest exchange rate
        }

        // Add this to the form submission event
        document.querySelector('form').addEventListener('submit', updateExchangeRate);
    </script>
</body>

</html>