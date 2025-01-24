<?php
session_start();
include '../database/db_connection.php';
include 'get_real_time_prices.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['clear_record'])) {
    $clear_stmt = $conn->prepare("DELETE FROM transactions_exchange WHERE user_id = ?");
    $clear_stmt->execute([$userId]); // Use $userId instead of $user_id
    header("Location: dashboard.php"); // Refresh the page
    exit();
}


// Fetch user's current balances for displaying
$sql = "SELECT USDT, BTC, ETH,USDC, BNB, XRP, DOGE, SOL, ADA, TRX, DOT, LTC, BCH, ETC, UNI, LINK, AVAX, NEO, EOS, ARB, APT, TON FROM user_balances WHERE user_id = $userId";
$balanceResult = $conn->query($sql);
$balance = $balanceResult->fetch_assoc();




// // Direct USD estimation
$usdtBalance = $balance['USDT'] ?? 0;
$btcBalance = $balance['BTC'] ?? 0;
$ethBalance = $balance['ETH'] ?? 0;
$usdcBalance = $balance['USDC'] ?? 0;
$bnbBalance = $balance['BNB'] ?? 0;
$xrpBalance = $balance['XRP'] ?? 0;
$dogeBalance = $balance['DOGE'] ?? 0;
$solBalance = $balance['SOL'] ?? 0;
$adaBalance = $balance['ADA'] ?? 0;
$trxBalance = $balance['TRX'] ?? 0;
$dotBalance = $balance['DOT'] ?? 0;
$ltcBalance = $balance['LTC'] ?? 0;
$bchBalance = $balance['BCH'] ?? 0;
$etcBalance = $balance['ETC'] ?? 0;
$uniBalance = $balance['UNI'] ?? 0;
$linkBalance = $balance['LINK'] ?? 0;
$avaxBalance = $balance['AVAX'] ?? 0;
$neoBalance = $balance['NEO'] ?? 0;
$eosBalance = $balance['EOS'] ?? 0;
$arbBalance = $balance['ARB'] ?? 0;
$aptBalance = $balance['APT'] ?? 0;
$tonBalance = $balance['TON'] ?? 0; // TON balance

$usdtUsd = $usdtBalance * $usdtPrice;
$btcUsd = $btcBalance * $btcPrice;
$ethUsd = $ethBalance * $ethPrice;
$usdcUsd = $usdcBalance * $usdcPrice;
$bnbUsd = $bnbBalance * $bnbPrice;
$xrpUsd = $xrpBalance * $xrpPrice;
$dogeUsd = $dogeBalance * $dogePrice;
$solUsd = $solBalance * $solPrice;
$adaUsd = $adaBalance * $adaPrice;
$trxUsd = $trxBalance * $trxPrice;
$dotUsd = $dotBalance * $dotPrice;
$ltcUsd = $ltcBalance * $ltcPrice;
$bchUsd = $bchBalance * $bchPrice;
$etcUsd = $etcBalance * $etcPrice;
$uniUsd = $uniBalance * $uniPrice;
$linkUsd = $linkBalance * $linkPrice;
$avaxUsd = $avaxBalance * $avaxPrice;
$neoUsd = $neoBalance * $neoPrice;
$eosUsd = $eosBalance * $eosPrice;
$arbUsd = $arbBalance * $arbPrice;
$aptUsd = $aptBalance * $aptPrice;
$tonUsd = $tonBalance * $tonPrice;

$balances = [
    $usdtUsd, $btcUsd, $ethUsd, $usdcUsd, $bnbUsd, $xrpUsd, $dogeUsd, $solUsd,
    $adaUsd, $trxUsd, $dotUsd, $ltcUsd, $bchUsd, $etcUsd, $uniUsd, $linkUsd,
    $avaxUsd, $neoUsd, $eosUsd, $arbUsd, $aptUsd, $tonUsd
];

$totalUsdValue = array_sum($balances);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="title_udash">User Dashboard</title>

    <!-- Style Links -->
    <!-- <link rel="stylesheet" href="../../style/userDashboard.css">
    <link rel="stylesheet" href="../../style/footer.css">
    <link rel="stylesheet" href="../../style/navigation.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="../../style/light-mode.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../js/translate.js"></script>

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
                        <a href="../../php/user_function/depo_adding.html"><img src="../../assets/images/deposit.png" alt="Deposit">
                            <span>
                                <h5 data-translate="nav_deposit">Deposit</h5>
                                <p data-translate="nav_deposit_desc">Crypto deposit and your records</p>
                            </span>
                        </a>

                        <!-- Withdraw -->
                        <a href="../../php/user_function/adding-withdraw.html"><img src="../../assets/images/withdraw.png" alt="Withdraw">
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
                        <a href="../../php/user_function/user_balance.php"><img src="../../assets/images/tax-credit.png" alt="User Balance Profile Pic">
                            <span>
                                <h5 data-translate="nav_user_profile">User Balance Profile</h5>
                                <p data-translate="nav_user_profile_desc">Your balance and your transaction history</p>
                            </span>
                        </a>

                        <!-- Coin Exchange -->
                        <a href="../../php/user_function/exchange.php"><img src="../../assets/images/exchange (1).png" alt="Spot Trading">
                            <span>
                                <h5 data-translate="nav_coin_exchange">Coin Exchange</h5>
                                <p data-translate="nav_coin_exchange_desc">Easily trade with any crypto combination</p>
                            </span>
                        </a>

                        <!-- Trend Trading -->
                        <a href="../../php/trade_order/btcusdt.php"><img src="../../assets/images/technical-analysis.png" alt="Margin Trading">
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
                            <a href="../../php/user_function/depo_adding.html">
                                <img src="../../assets/images/deposit.png" alt="deposit logo" class="w-8">
                                <span data-translate="nav_deposit">Deposit</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../php/user_function/adding-withdraw.html">
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
                            <a href="../../php/user_function/user_balance.php">
                                <img src="../../assets/images/tax-credit.png" alt="User Balance Profile logo" class="w-8">
                                <span data-translate="nav_user_profile">User Balance Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../php/user_function/exchange.php">
                                <img src="../../assets/images/exchange.png" alt="Coin Exchange logo" class="w-8">
                                <span data-translate="nav_coin_exchange">Coin Exchange</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../php/trade_order/btcusdt.php">
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
                    <a href="../../php/trade_order/btcusdt.php" class="w-[100%] bg-[#78B7D0] text-center rounded-md" data-translate="nav_trade_now">Trade
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
                            </h5>                        </li>
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
    <br><br><br><br><br>
    <!-- Main Container -->
    <main class="main-container1">
        <!-- Balance Info Div -->
        <div class="balance-info-container">
            <!-- Amount Div -->
            <div>
            <h1 class="text-5xl font-bold" data-translate="custody_dashboard">Custody Dashboard</h1>
                <h2 class="text-4xl font-bold my-5">$<?php echo number_format($totalUsdValue, 2); ?></h2>
                <h4 class="text-[14px]">$ (6.78%) <i class="fa-solid fa-arrow-trend-up"></i></h4>
            </div>
            <a href="../../recordsHistory.php"><button class="py-2 px-8 bg-gray-300 rounded-3xl text-black" data-translate="view_history">View
                    History</button></a>
        </div>
        <br><br>
        <!-- Balances Details Container -->
        <div class="balance-details-container">
            <h1 class="text-2xl font-bold" data-translate="balances">Balances</h1>
            <br>
            <h2 class="text-1xl py-1 px-10 rounded-2xl bg-black w-fit text-gray-100" data-translate="crypto_balances">Crypto Balances</h2>
            <br>
            <!-- Balance Table -->
            <table class="balance-table">
                <thead>
                    <tr class="text-right">
                        <th class="text-left" data-translate="asset">Asset</th>
                        <th data-translate="balances">Balance</th>
                        <th data-translate="estimate_price">Estimate Price</th>
                        <th class="mt-th"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Coin Datas -->
                    <tr class="text-right table-row">
                        <td class="text-left font-bold"><img src="../../images/BTC.png" alt="Coin Icon"
                                class="inline-block mr-3"> Bitcoin</td>
                        <td> <span id="BTC_balance">
                                <?php echo $btcBalance; ?> BTC</td>
                        <td>$
                            <?php echo number_format($btcUsd, 2); ?> USD</span>
                        </td>
                        <td class="special-td">
                            <div>
                                <a href="./depo_btc.php"><button
                                        class="deposit-btn" data-translate="deposit">Deposit</button></a>
                                <a href="./withdraw_btc.php"><button
                                        class="withdraw-btn" data-translate="withdraw">Withdraw</button></a>

                            </div>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left font-bold"><img src="../../images/ETH.png" alt="Coin Icon"
                                class="inline-block mr-3"> Ethereum</td>
                        <td><span id="ETH_balance">
                                <?php echo $ethBalance; ?> ETH</td>
                        <td>$
                            <?php echo number_format($ethUsd, 2); ?> USD
                        </td>
                        <td class="special-td">
                            <div>
                                <a href="./depo_eth.php"><button
                                        class="deposit-btn" data-translate="deposit">Deposit</button></a>
                                <a href="./withdraw_eth.php"><button
                                        class="withdraw-btn" data-translate="withdraw">Withdraw</button></a>

                            </div>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left font-bold"><img src="../../images/USDT.png" alt="Coin Icon"
                                class="inline-block mr-3"> Tether</td>
                        <td><span id="USDT_balance">
                                <?php echo $usdtBalance; ?> USDT</td>
                        <td>$
                            <?php echo number_format($usdtUsd, 2); ?> USD
                        </td>
                        <td class="special-td">
                            <div>
                                <a href="./depo_usdt.php"><button
                                        class="deposit-btn" data-translate="deposit">Deposit</button></a>
                                <a href="./withdraw_usdt.php"><button
                                        class="withdraw-btn" data-translate="withdraw">Withdraw</button></a>

                            </div>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left font-bold"><img src="../../images/USDC.png" alt="Coin Icon"
                                class="inline-block mr-3"> USD</td>
                        <td><span id="USDC_balance">
                                <?php echo $usdcBalance; ?> USDC</td>
                        <td>$
                            <?php echo number_format($usdcUsd, 2); ?> USD
                        </td>
                        <td class="special-td">
                            <div>
                                <a href="./depo_usdc.php"><button
                                        class="deposit-btn" data-translate="deposit">Deposit</button></a>
                                <a href="./withdraw_usdc.php"><button
                                        class="withdraw-btn" data-translate="withdraw">Withdraw</button></a>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <h2 class="text-1xl py-1 px-10 rounded-2xl bg-black w-fit text-gray-100" data-translate="other_coin_balances">Other Coin Balances</h2>
            <br>
            <div class="scroll-area">
                <table class="balance-table">
                    <thead>
                        <tr class="text-right">
                            <th class="text-left" data-translate="asset">Asset</th>
                            <th data-translate="balances">Balance</th>
                            <th data-translate="estimate_price">Estimate Prices</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Coin Datas -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/BNB.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Binance</td>
                            <td> <span id="BNB_balance">
                                    <?php echo $bnbBalance; ?> BNB</td>
                            <td>$
                                <?php echo number_format($bnbUsd, 2); ?> USD</span>
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/XRP.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Ripple</td>
                            <td><span id="XRP_balance">
                                    <?php echo $xrpBalance; ?> XRP</td>
                            <td>$
                                <?php echo number_format($xrpUsd, 2); ?> USD
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/DOGE.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Dogecoin</td>
                            <td><span id="DOGE_balance">
                                    <?php echo $dogeBalance; ?> DOGE</td>
                            <td>$
                                <?php echo number_format($dogeUsd, 2); ?> USD
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/SOL.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Solana</td>
                            <td><span id="SOL_balance">
                                    <?php echo $solBalance; ?> SOL</td>
                            <td>$
                                <?php echo number_format($solUsd, 2); ?> USD
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/ADA.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Cardano</td>
                            <td> <span id="ADA_balance">
                                    <?php echo $adaBalance; ?> ADA</td>
                            <td>$
                                <?php echo number_format($adaUsd, 2); ?> USD</span>
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/TRX.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Tron</td>
                            <td><span id="TRX_balance">
                                    <?php echo $trxBalance; ?> TRX</td>
                            <td>$
                                <?php echo number_format($trxUsd, 2); ?> USD
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/DOT.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Polkadot</td>
                            <td><span id="DOT_balance">
                                    <?php echo $dotBalance; ?> DOT</td>
                            <td>$
                                <?php echo number_format($dotUsd, 2); ?> USD
                            </td>
    
                        </tr>
    
                        <tr class="text-right table-row">
                            <td class="text-left font-bold"><img src="../../images/LTC.png" alt="Coin Icon"
                                    class="inline-block mr-3"> Litecoin</td>
                            <td><span id="LTC_balance">
                                    <?php echo $ltcBalance; ?> LTC</td>
                            <td>$
                                <?php echo number_format($ltcUsd, 2); ?> USD
                            </td>
    
                        </tr>
                        <!-- Bitcoin Cash -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/BCH.png" alt="Coin Icon" class="inline-block mr-3"> Bitcoin Cash
                            </td>
                            <td><span id="BCH_balance">
                                    <?php echo $bchBalance; ?> BCH
                                </span></td>
                            <td>$
                                <?php echo number_format($bchUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Ethereum Classic -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/ETC.png" alt="Coin Icon" class="inline-block mr-3"> Ethereum Classic
                            </td>
                            <td><span id="ETC_balance">
                                    <?php echo $etcBalance; ?> ETC
                                </span></td>
                            <td>$
                                <?php echo number_format($etcUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Uniswap -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/UNI.png" alt="Coin Icon" class="inline-block mr-3"> Uniswap
                            </td>
                            <td><span id="UNI_balance">
                                    <?php echo $uniBalance; ?> UNI
                                </span></td>
                            <td>$
                                <?php echo number_format($uniUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Chainlink -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/LINK.png" alt="Coin Icon" class="inline-block mr-3"> Chainlink
                            </td>
                            <td><span id="LINK_balance">
                                    <?php echo $linkBalance; ?> LINK
                                </span></td>
                            <td>$
                                <?php echo number_format($linkUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Avalanche -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/AVAX.png" alt="Coin Icon" class="inline-block mr-3"> Avalanche
                            </td>
                            <td><span id="AVAX_balance">
                                    <?php echo $avaxBalance; ?> AVAX
                                </span></td>
                            <td>$
                                <?php echo number_format($avaxUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- NEO -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/NEO.png" alt="Coin Icon" class="inline-block mr-3"> NEO
                            </td>
                            <td><span id="NEO_balance">
                                    <?php echo $neoBalance; ?> NEO
                                </span></td>
                            <td>$
                                <?php echo number_format($neoUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- EOS -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/EOS.png" alt="Coin Icon" class="inline-block mr-3"> EOS
                            </td>
                            <td><span id="EOS_balance">
                                    <?php echo $eosBalance; ?> EOS
                                </span></td>
                            <td>$
                                <?php echo number_format($eosUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Arbitrum -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/ARB.png" alt="Coin Icon" class="inline-block mr-3"> Arbitrum
                            </td>
                            <td><span id="ARB_balance">
                                    <?php echo $arbBalance; ?> ARB
                                </span></td>
                            <td>$
                                <?php echo number_format($arbUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Aptos -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/APT.png" alt="Coin Icon" class="inline-block mr-3"> Aptos
                            </td>
                            <td><span id="APT_balance">
                                    <?php echo $aptBalance; ?> APT
                                </span></td>
                            <td>$
                                <?php echo number_format($aptUsd, 2); ?> USD
                            </td>
                        </tr>
    
                        <!-- Toncoin -->
                        <tr class="text-right table-row">
                            <td class="text-left font-bold">
                                <img src="../../images/TON.png" alt="Coin Icon" class="inline-block mr-3"> Toncoin
                            </td>
                            <td><span id="TON_balance">
                                    <?php echo $tonBalance; ?> TON
                                </span></td>
                            <td>$
                                <?php echo number_format($tonUsd, 2); ?> USD
                            </td>
                        </tr>
    
                    </tbody>
                </table>
            </div>
            <br>
        </div>
    </main>

    <!-- Footer -->
   <footer>
        <div class="align-margin footer-sec"> <!-- to align margin -->
            <!-- Contact Icon Section -->
            <div class="contact-container w-[30%] mb-5">
                <span>
                    <div><img src="../../assets/images/LOGO.png" alt="LOGO" class="web-logo">
                        <h1 class="text-2xl font-bold" data-translate="footer_logo_text">Bithumbnn</h1>
                    </div>
                    <p data-translate="footer_slogan">Making Crypto Trading Easier</p>
                </span>

                <!-- Icon Container -->
                <span class="icon-container">
                    <!-- Telegram Account -->
                    <a href="#"><img src="../../assets/images/telegram.png" alt="Telegram" class="contact-icons"></a>
                    <!-- Email -->
                    <a href="#"><img src="../../assets/images/gmail.png" alt="Email" class="contact-icons"></a>
                    <p data-translate="footer_copyright">© 2024-2025 Bithumbnn.com. All rights reserved.</p>
                </span>
            </div>

            <!-- About -->
            <ul>
                <span class="gen-info-container" onclick="myFunction_gsm('sec1')">
                    <h2 class="text-2xl font-bold mb-5" data-translate="footer_about">About</h2><i class="fa-solid fa-angle-right" id="right-arrow1"></i>
                </span>
                <span id="sec1">
                    <li><a href="../../helpCenter.php" data-translate="footer_about_us">About us</a></li>
                    <li><a href="#" data-translate="footer_contact_us">Contact Us</a></li>
                    <li><a href="../../termAgreement.php" data-translate="footer_terms_policy">Terms and policy</a></li>
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
                    <li><a href="../../helpCenter.php" data-translate="footer_help_center">Help Center</a></li>
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

    <!-- ===================================JavaScripts=============================== -->

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
</body>

</html>