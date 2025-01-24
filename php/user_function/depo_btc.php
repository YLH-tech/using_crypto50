<?php
session_start();
include '../database/db.php';
include '../database/db_connection.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Clear history if the user clicks "Clear"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_deposit_record'])) {
    // SQL query to update the show_history column in the orders table
    $update_deposit_sql = "UPDATE transactions SET show_history = 'no' WHERE user_id = ? AND action = 'request'";
    
    if ($stmt_deposit = $conn->prepare($update_deposit_sql)) {
        $stmt_deposit->bind_param("i", $user_id);
        $stmt_deposit->execute();
        $stmt_deposit->close();
    }

    // Redirect to the same page to reflect the changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetch();

// Fetch transaction history (only records visible to user)
$history_stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND show_history = 'yes' ORDER BY timestamp DESC");
$history_stmt->execute([$user_id]);
$transactions = $history_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="deposit">Deposit</title>
    <link id="themeStylesheet" rel="stylesheet" href="./light-mode.css">


    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="./darkMode.js"></script>
    <script src="../../js/translate.js"></script>

    <!-- Uploading File -->
    <script src="../../js/fileUpload.js"></script>
    <style>
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

    <br>

    <!-- Deposite main section -->
    <main>
        <h1 class="text-5xl my-5" data-translate="deposit">Deposit</h1>
        <?php if (isset($_SESSION['successMsg'])): ?>
            <div class="success" data-translate="<?= $_SESSION['successMsg']; ?>">
                <?php unset($_SESSION['successMsg']); ?>
            </div>
        <?php endif; ?>


        <div class="main-section">
            <!-- Sub Container -->
            <div class="main-container">
                <!-- QR container -->
                <div class="qr-container">
                    <img src="../../images/BTC_QR.jpg" alt="BTC Coin QR" class="qr-img">
                    <p class="qr-explain" data-translate="qrExplanation_btc">Please, Scan the QR for BTC Wallet Address</p>
                    <br>
                    <!-- Wallet Code -->
                    <div class="m-auto w-fit">
                        <input type="text" value="bc1q6kl48f064md7tl2jsje3y24rsk2ps96e86edn2" id="wallet-code"
                            class="code-container" disabled>
                        <button class="copy-btn" onclick="code_copying()"><i
                                class="fa-solid fa-copy text-2xl"></i></button>
                    </div>
                </div>

                <ol class="list-decimal text-2xl">
                    <!-- Processing div -->
                    <form action="../../admin/req_coin_btc.php" method="post" enctype="multipart/form-data">
                        <div class="sub-container">
                            <li hidden>
                                <h3 class="text-2xl mb-5"></h3>
                                <select name="coin_type" id="coin-select" class="data-inputs">

                                    <option value="BTC">BTC</option>

                                </select>
                            </li><br>

                            <li>
                                <h3 class="text-2xl my-5" data-translate="generateDepositAddress">Generate Deposit Address</h3>
                                <p class="text-[18px]" data-translate="selectNetwork">Select Network</p>

                                <!-- Input section -->
                                <div class="input-section">
                                    <!-- For showing selected coin -->
                                    <h3 class="selected-coin flex items-center gap-2 bg-grey-custom w-[300px]">
                                        <img src="../../images/BTC.png" class="w-8" alt="BTC">Bitcoin
                                    </h3><br><br>

                                    <!-- For input amount -->
                                    <input class="bg-grey-custom" data-translate="enterAmount" placeholder="Enter amount" id="coin-amount"
                                        type="number" step="0.0001" name="amount">


                                    <input type="file" name="image" accept=".jpeg,.jpg,.png,.heic" id="file-upload"
                                        onchange="showUploadedFile()" hidden>

                                </div><br>
                            </li>
                        </div>
                        <br><br>
                        <input type="submit" id="request-btn" class="text-2xl req-btn" value="Request" hidden>

                    </form>

                    <li>
                        <!-- Image preview area -->
                        <div class="card">
                            <div class="drop_box" id="drop-box">
                                <!-- Image preview area (hidden initially) -->
                                <img id="imagePreview" src="#" alt="Selected Image"
                                    style="display: none; max-width: 400px; margin-top: 10px;">

                                <!-- Hidden input for file upload -->
                                <input type="file" accept=".jpeg,.jpg,.png,.heic" id="file-upload"
                                    onchange="showUploadedFile()" hidden>

                                <!-- Instructions text -->
                                <header id="instructions">
                                    <h4 data-translate="selectFile">Select File here to verify.</h4>
                                    <p data-translate="supportedFiles">Files Supported: .JPEG, .JPG, .PNG, .HEIC</p>
                                </header>
                                <!-- "Choose File" button -->
                                <h3 class="btn" id="upload-btn" data-translate="chooseFile">Chose File</h3>
                            </div>
                        </div>
                    </li>
                    <br>
                    <button class="req-btn" onclick="document.getElementById('request-btn').click();" data-translate="request">Request</button>
                </ol>
            </div>

            <!-- Your Balance -->
            <div class="coin-price" style="height: 1020px;">
                <h2 class="text-4xl" data-translate="your_balance">Your Balance</h2>
                <br>
                <!-- Table for coin price -->
                <table class="coin-table">
                    <thead>
                        <tr>
                            <th class="text-left text-2xl" data-translate="coin">Coin</th>
                            <th class="text-right text-2xl" data-translate="price">Price</th>
                        </tr>
                    </thead>
                    <tbody class="scrollable-area" style="height: 900px;">
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
                            <td class="coins"><img src="../../images/AVAX.png" alt="AVA icon"> AVAX: </td>
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
    <!-- Transaction History -->
    <section class="transaction-history">
        <h1 class="text-3xl" data-translate="transactionHistory">Transaction History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white my-3" name="clear_deposit_record"
                onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                History</button>
        </form>
        <br>
        <br>
        <div class="horizontal-scroll">
            <table>
                <thead>
                    <tr>
                        <th class="rounded-l-md" data-translate="action">Action</th>
                        <th data-translate="coinType">Coin Type</th>
                        <th data-translate="amount">Amount</th>
                        <th data-translate="status">Status</th>
                        <th data-translate="adminNote">Admin Note</th>
                        <th class="rounded-r-md" data-translate="date">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-gray-500" data-translate="noTransactionHistory">No transaction history available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                            <td data-translate="<?= strtolower($transaction['action']) ?>">
                                <?= ucfirst($transaction['action']) ?>
                            </td>
                                <td>
                                    <?= strtoupper($transaction['coin_type']) ?>
                                </td>
                                <td>
                                    <?= $transaction['amount'] ?>
                                </td>
                                <td data-translate="<?= $transaction['status'] === 'approved' ? 'approved' : ($transaction['status'] === 'rejected' ? 'rejected' : '') ?>">
                                    <?= ucfirst($transaction['status']) ?>
                                </td>
                                <td data-translate="<?= $transaction['status'] === 'approved' ? 'deposit_approved' : ($transaction['status'] === 'rejected' ? 'deposit_rejected' : '') ?>">
                                    <?= nl2br($transaction['admin_note']) ?>
                                </td>
                                <td>
                                    <?= $transaction['timestamp'] ?>
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
    <!-- Script functions -->
    <script src="../../js/dropdownScript.js"></script> <!-- drop down btn script -->

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
        $('#upload-btn').click(function() {
            $('#file-upload').click();
        });

        function code_copying() {
            // Get the text field
            var copyText = document.getElementById("wallet-code");

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);

            // Alert the copied text
            alert("Copied the btc code : " + copyText.value);
        }
    </script>
</body>

</html>