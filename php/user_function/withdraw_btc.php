<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_withdraw_record'])) {
    $update_withdraw_sql = "UPDATE withdrawal_requests SET show_history = 'no' WHERE user_id = ?";
    $stmt_withdraw = $pdo->prepare($update_withdraw_sql);
    $stmt_withdraw->execute([$userId]);

    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Fetch all withdrawal requests
$stmt = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE user_id = ? AND show_history = 'yes' ORDER BY created_at DESC");
$stmt->execute([$userId]);  // Bind the session user's ID
$withdrawalHistory = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="withdraw">Withdraw</title>

    <!-- Style link -->
    <!-- <link rel="stylesheet" href="../../style/deposit_withdraw.css">
    <link rel="stylesheet" href="../../style/navigation.css">
    <link rel="stylesheet" href="../../style/footer.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="./light-mode.css">


    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="./darkMode.js"></script>
    <script src="../../js/translate.js"></script>

    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid var(--iconic-color--);
            width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        .success {
            background: #F2DEDE;
            color: green;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
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
    <!-- Withdraw main section -->
    <main>
        <h1 class="text-5xl my-5" data-translate="withdraw">Withdraw</h1>
        <?php if (isset($_SESSION['successMsg'])): ?>
            <div class="success" data-translate="<?= $_SESSION['successMsg']; ?>">
                <?php unset($_SESSION['successMsg']); ?>
            </div>
        <?php endif; ?>

        <!-- Main Section -->
        <div class="main-section">
            <!-- Sub Container -->
            <div class="main-container withdraw-container">
                <!-- Showing amount -->
            <div class="showing-amount">
                <h1 data-translate="serviceCharge">Service Charge : </h1><span id="serviceCharge">0.000000</span>
                <br>
                <h1 data-translate="availableAmount">Avaliable Amount : </h1> <span id="availableAfterDeduction">0.000000</span>
            </div>
                <br>
                <!-- Processing div -->
                <form method="POST" id="withdrawalForm" action="process_withdrawal_btc.php">
                    <div class="sub-container">
                        <ol class="list-decimal text-2xl">
                            <li hidden>
                                <label for="coin-select" class="text-2xl" data-translate="selectCoin">Select Coin/ Token</label>
                                <br>
                                <select name="coin" id="coin" class="data-inputs" required onchange="updateBalance()">
                                    <option value="BTC">BTC</option>
                                </select>
                            </li>
                            <br>
                            <li>
                                <label for="wallet-add" class="text-2xl" data-translate="walletAddress">Wallet Address</label>
                                <br>
                                <input type="text" name="wallet_address" id="wallet_address" class="data-inputs" placeholder="Enter wallet address" data-translate="enterWalletAddress">
                            </li>
                            <br>
                            <li>
                                <h3 class="text-2xl" data-translate="withdrawAmount">Withdraw Amount</h3>
                                <p class="text-[18px]" data-translate="selectNetwork">Select Network</p>
    
                                <!-- Input section -->
                                <div class="input-section">
                                    <!-- For showing selected coin -->
                                    <h3 class="selected-coin flex items-center gap-2 bg-grey-custom w-[300px]">
                                        <img src="../../images/BTC.png" class="w-8 dynamic-image" alt="BTC">Bitcoin
                                    </h3>
    
                                    <!-- For input amount -->
                                    <input type="number" name="amount" id="coin-amount" class="bg-grey-custom" placeholder="Amount" data-translate="amount" step="0.0001"  oninput="calculateServiceCharge()">
    
                                    <button type="button" class="all-btn" onclick="setMaxAmount()" data-translate="all">All</button>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <p id="errorMessage" style="color: red;"></p>
                    <br><br>
                    <button type="button" class="text-2xl req-btn" onclick="validateAndShowModal()" data-translate="withdraw">Withdraw</button>
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
        </div>
        
    </main>
    <br><br><br>

    <!-- Modal for entering fund password -->
    <div id="fundPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFundPasswordModal()">&times;</span>
            <h1 class="text-3xl" data-translate="enterFundPassword">Enter Fund Password</h1>

            <input type="password" class="data-inputs" id="fundPassword" placeholder="Fund Password" data-translate="fundPassword" required>
            <br><br>
            <button type="button" class="text-2xl req-btn" onclick="submitWithdrawal()" data-translate="submit">Submit</button>
        </div>
    </div>
    <!-- Transaction History -->
    <section class="transaction-history">
        <h1 class="text-3xl" data-translate="transactionHistory">Transaction History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white my-3" name="clear_withdraw_record"
            onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                History</button>
        </form>
        <br>
        <br>
        <div class="horizontal-scroll">
            <table>
                <thead>
                    <tr>
                        <th class="rounded-l-md" data-translate="date">Date</th>
                        <th data-translate="coin">Coin</th>
                        <th data-translate="amount">Amount</th>
                        <th data-translate="walletAddress">Wallet Address</th>
                        <th data-translate="serviceCharges">Service Charge</th>
                        <th data-translate="netAmount">Net Amount</th>
                        <th class="rounded-r-md" data-translate="status">Status</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (empty($withdrawalHistory)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-500" data-translate="noTransactionHistory">No transaction history available.</td>
                    </tr>
                <?php else: ?>
                <?php foreach ($withdrawalHistory as $request): ?>
                    <tr>
                        <td><?php echo $request['created_at']; ?></td>
                        <td><?php echo htmlspecialchars($request['coin']); ?></td>
                        <td><?php echo number_format($request['amount'], 4); ?></td>
                        <td class="wallet-address"><?php echo htmlspecialchars($request['wallet_address']); ?></td>
                        <td><?php echo number_format($request['service_charge'], 4); ?></td>
                        <td><?php echo number_format($request['net_amount'], 4); ?></td>
                        <td data-translate="<?= htmlspecialchars($request['status']); ?>">
                            <?= htmlspecialchars($request['status']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </section>

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
    
    <script>
        const balances = {
            USDT: <?php echo $balance['USDT']; ?>,
            BTC: <?php echo $balance['BTC']; ?>,
            ETH: <?php echo $balance['ETH']; ?>,
            USDC: <?php echo $balance['USDC']; ?>
        };

        function calculateServiceCharge() {
            const amount = parseFloat(document.getElementById('coin-amount').value) || 0;
            const serviceCharge = 0.01 * amount;
            const netAmount = amount - serviceCharge;

            document.getElementById('serviceCharge').textContent = serviceCharge.toFixed(6);
            document.getElementById('availableAfterDeduction').textContent = netAmount.toFixed(6);
        }

        function updateBalance() {
            document.getElementById('coin-amount').value = '';
            calculateServiceCharge();
        }

        function setMaxAmount() {
            const selectedCoin = document.getElementById('coin').value;
            const maxAmount = balances[selectedCoin];
            document.getElementById('coin-amount').value = maxAmount.toFixed(6);
            calculateServiceCharge();
        }

        async function validateAndShowModal() {
            const coin = document.getElementById('coin').value;
            const amount = parseFloat(document.getElementById('coin-amount').value);
            const walletAddress = document.getElementById('wallet_address').value;
            const errorMessage = document.getElementById('errorMessage');

            errorMessage.textContent = '';

            // Fetch translations
            const language = localStorage.getItem('selectedLanguage') || 'en';
            const translations = await loadTranslation(language);

            if (!walletAddress) {
                errorMessage.textContent = translations["Please enter a wallet address."] || "Please enter a wallet address.";
                return;
            }

            if (isNaN(amount) || amount <= 0) {
                errorMessage.textContent = translations["Withdrawal amount must be greater than zero."] || "Withdrawal amount must be greater than zero.";
                return;
            }

            if (balances[coin] < amount) {
                errorMessage.textContent = translations["Insufficient balance for this withdrawal."] || "Insufficient balance for this withdrawal.";
                return;
            }

            // If no errors, show the password modal
            showFundPasswordModal();
        }


        function showFundPasswordModal() {
            document.getElementById('fundPasswordModal').style.display = 'block';
        }

        function closeFundPasswordModal() {
            document.getElementById('fundPasswordModal').style.display = 'none';
        }

        function submitWithdrawal() {
            const fundPassword = document.getElementById('fundPassword').value;

            if (!fundPassword) {
                alert("Please enter the fund password.");
                return;
            }

            const fundPasswordInput = document.createElement('input');
            fundPasswordInput.type = 'hidden';
            fundPasswordInput.name = 'fund_password';
            fundPasswordInput.value = fundPassword;

            const form = document.getElementById('withdrawalForm');
            form.appendChild(fundPasswordInput);

            form.submit();
        }
    </script>

</body>

</html>