<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Improved query to fetch user balances using prepared statement
$sql = "SELECT USDT, BTC, ETH, USDC, BNB, XRP, DOGE, SOL, ADA, TRX, DOT, LTC, BCH, ETC, UNI, LINK, AVAX, NEO, EOS, ARB, APT, TON 
        FROM user_balances 
        WHERE user_id = ?";
$balanceStmt = $conn->prepare($sql);
$balanceStmt->bind_param("i", $userId); // 'i' means integer (user_id is assumed to be an integer)
$balanceStmt->execute();
$balanceResult = $balanceStmt->get_result();
$balance = $balanceResult->fetch_assoc();

// Process the form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['clear_record'])) {
    // Get the submitted coin amount and coin type
    $amount = $_POST['amount'];
    $fromCoin = $_POST['from_coin'];
    $toCoin = $_POST['to_coin'];

    // Check if the amount is greater than the balance or zero
    if ($amount <= 0 || $amount > $balance[$fromCoin]) {
        $_SESSION['errorMsg'] = "Insufficient balance.";
        header('Location: exchange.php'); // Redirect to the exchange page with error message
        exit();
    }

    // Calculate the amount to receive based on the exchange rate
    $exchangeRate = $_POST['exchange_rate']; // Passed via AJAX or hidden input
    $receivedAmount = $amount * $exchangeRate;

    // Prepare the database query to update the balances
    $updateStmt = $conn->prepare(
        "UPDATE user_balances 
         SET $fromCoin = $fromCoin - ?, $toCoin = $toCoin + ? 
         WHERE user_id = ?"
    );
    $updateStmt->bind_param("ddi", $amount, $receivedAmount, $userId); // Assuming $amount and $receivedAmount are floats, and userId is an integer.
    $updateStmt->execute();
  
    // Record the transaction in the transactions_exchange table
    $toCoinAmount = $receivedAmount; // The amount you received after the exchange
    $rate = $exchangeRate; // The exchange rate

    // Prepare the SQL query to record the transaction
    $transactionStmt = $conn->prepare(
        "INSERT INTO transactions_exchange (user_id, from_coin, to_coin, from_amount, to_amount, rate) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $transactionStmt->bind_param("issdds", $userId, $fromCoin, $toCoin, $amount, $toCoinAmount, $rate);

    // Execute the statement
    $transactionStmt->execute();

    // Redirect to the dashboard or the same page with success message
    $_SESSION['errorMsg'] = "Exchange successful!.Successfully, $amount $fromCoin exchanged to $toCoinAmount $toCoin.";
    header('Location: exchange.php');
    exit();
}

// Fetch user's exchange records
$recordsSql = "SELECT from_coin, to_coin, from_amount, to_amount, rate, transaction_date 
                FROM transactions_exchange WHERE user_id = $userId ORDER BY transaction_date DESC";
$recordsResult = $conn->query($recordsSql);

// Check if the "clear_record" form is submitted
if (isset($_POST['clear_record'])) {
    // Delete transaction records for the current user
    $deleteStmt = $conn->prepare("DELETE FROM transactions_exchange WHERE user_id = ?");
    $deleteStmt->bind_param("i", $userId); // Bind the user_id as an integer
    $deleteStmt->execute();
    
    // Redirect back to the page
    header('Location: exchange.php');
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Exchange</title>

    <!-- Styles -->
    <link rel="stylesheet" href="../../style/exchange.css">
    <link rel="stylesheet" href="../../style/navigation.css">
    <link rel="stylesheet" href="../../style/footer.css">

        <!-- Fontawesome link -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .error {
            background: #F2DEDE;
            color: #008000;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }

        .horizontal-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;

            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .horizontal-container div {
            margin: 0 10px;
        }
    </style>

</head>

<body>
     <!-- night mode script -->
     <script src="./js/darkMode.js"></script>

<!-- Navigation Bar -->
<nav>
    <!-- Left-side menus -->
    <div class="nav-subdiv">

        <!-- Web Logo -->
        <div class="flex items-center">

            <a href="../../index.html"><img src="../../assets/images/" alt="LOGO" class="web-logo"></a>
            <h1 class="text-2xl font-bold">Bithumbnn</h1>
        </div>

        <div id="non-mobile" hidden>
            <!-- Buy Crypto -->
            <div class="dropdown">
                <button class="dropbtn">Buy Crypto <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                <div class="dropdown-content">

                    <!-- Deposit -->
                    <a href="../../CoinsDeposit.html"><img src="../../assets/images/deposit.png" alt="Deposit">
                        <span>
                            <h5>Deposit</h5>
                            <p>Crypto deposit and your records</p>
                        </span>
                    </a>

                    <!-- Withdraw -->
                    <a href="../../CoinsWithdraw.html"><img src="../../assets/images/withdraw.png" alt="Withdraw">
                        <span>
                            <h5>Withdraw</h5>
                            <p>Crypto withdraw and your records</p>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Markets -->
            <div class="dropdown">
                <button class="dropbtn">Markets <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                <div class="dropdown-content">

                    <!-- Market Data -->
                    <a href="./marketData.html"><img src="../../assets/images/web-analytics.png" alt="Market Data">
                        <span>
                            <h5>Market Data</h5>
                            <p>Capture market opportunities</p>
                        </span>
                    </a>

                    <!-- Feed -->
                    <a href="./cryptoNews.html"><img src="../../assets/images/feed.png" alt="Feed">
                        <span>
                            <h5>Feed</h5>
                            <p>Discover current trends</p>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Exchange -->
            <div class="dropdown">
                <button class="dropbtn">Exchange <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                <div class="dropdown-content">
                    <!-- User Balance Profile -->
                    <a href="#"><img src="../../assets/images/tax-credit.png" alt="User Balance Profile Pic">
                        <span>
                            <h5>User Balance Profile</h5>
                            <p>Your balance and your transaction history</p>
                        </span>
                    </a>

                    <!-- Coin Exchange -->
                    <a href="#"><img src="../../assets/images/exchange (1).png" alt="Spot Trading">
                        <span>
                            <h5>Coin Exchange</h5>
                            <p>Easily trade with any crypto combination</p>
                        </span>
                    </a>

                    <!-- Trend Trading -->
                    <a href="./php/Coins/btcusdt.php"><img src="../../assets/images/technical-analysis.png"
                            alt="Margin Trading">
                        <span>
                            <h5>Trend Trading</h5>
                            <p>The most popular trading nowaday</p>
                        </span>
                    </a>
                </div>
            </div>

            <!-- More -->
            <div class="dropdown">
                <button class="dropbtn">More <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                <div class="dropdown-content">
                    <!-- Help Center -->
                    <a href="./helpCenter.html"><img src="../../assets/images/help.png" alt="Help Center Pic">
                        <span>
                            <h5>Help Center</h5>
                            <p>ready to help you</p>
                        </span>
                    </a>
                    
                    <!-- Records History -->
                    <a href="./records_history.html"><img src="../../assets/images/clock.png" alt="History Pic">
                        <span>
                            <h5>Records History</h5>
                            <p>Footprints are here</p>
                        </span>
                    </a>

                    <!-- Customer Service -->
                    <a href="#"><img src="../../assets/images/insurance-agent.png" alt="Customer Service">
                        <span>
                            <h5>Customer Service</h5>
                            <p>Ready to help you</p>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="resp-clickdown" id="mobile" hidden>
            <button class="resp-clickbtn" onclick="resp_clickdown()"><i
                    class="fa-solid fa-braille ml-3 text-1xl align-middle"></i></button>
            <div id="resp-myDropdown" class="resp-clickdown-content">
                <!-- Academy -->
                <a href="#"><img src="../../assets/images/mortarboard.png" alt="Academy">
                    <span>
                        <h5>Academy</h5>
                        <p>Master cryptocurrency knowledge</p>
                    </span>
                </a>

                <!-- Academy -->
                <a href="#"><img src="../../assets/images/mortarboard.png" alt="Academy">
                    <span>
                        <h5>Academy</h5>
                        <p>Master cryptocurrency knowledge</p>
                    </span>
                </a>

                <!-- Academy -->
                <a href="#"><img src="../../assets/images/mortarboard.png" alt="Academy">
                    <span>
                        <h5>Academy</h5>
                        <p>Master cryptocurrency knowledge</p>
                    </span>
                </a>

                <!-- Academy -->
                <a href="#"><img src="../../assets/images/mortarboard.png" alt="Academy">
                    <span>
                        <h5>Academy</h5>
                        <p>Master cryptocurrency knowledge</p>
                    </span>
                </a>

            </div>
        </div>
    </div>



    <!-- Right-side menu -->
    <div class="nav-subdiv">
        <a href="./login.html">Log In</a>
        <a href="./signup.html"><span class="signup-btn">Sign Up</span></a>

        <!-- <img src="../../assets/images/avatar.png" alt="Profile" class="profile-img"> -->
        <a href="./profile.html"><img src="../../assets/images/profile.png" alt="profile" class="w-12 inline-block"></a>
        <!-- <span class="border-l-2 border-r-2">
            <a href="#" class="mx-5"><i class="fa-solid fa-download"></i></a>
            <a href="#" class="mx-5" type="button" id="dark-switch"><i class="fa-solid fa-moon"
                    id="mode-btn"></i></a>
        </span>
        <a href="#" class="mx-5"><i class="fa-solid fa-globe"></i></a> -->

        <div class="clickdown">
            <button onclick="clickdown()"><i class="fa-solid fa-bars text-3xl align-middle clickbtn"></i></button>
            <div id="myDropdown" class="clickdown-content">
                <!-- Menu Options -->
                <ul class="w-[90%] m-auto text-white mobile">
                    <li>
                        <a href="#"><i class="fa-brands fa-gg-circle text-2xl mr-3"></i>Deposit</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-people-arrows text-2xl mr-3"></i>Withdraw</a>
                    </li>
                    <li>
                        <a href="./marketData.html"><i
                                class="fa-solid fa-magnifying-glass-chart text-2xl mr-3"></i>Market Data</a>
                    </li>
                    <li>
                        <a href="cryptoNews.html"><i class="fa-solid fa-square-poll-vertical text-2xl mr-3"></i>Feed</a>
                    </li>
                </ul>

                <!-- Sign Up / Login div -->
                <div class="signup-login-container">
                    <a href="./signup.html" id="signup">Sign Up</a>
                    <a href="./login.html" id="login">Log In</a>
                </div>

                <!-- Setting Options -->
                <ul class="w-[90%] m-auto text-white">
                    <li>
                        <h5><i class="fa-solid fa-language mr-3 text-2xl"></i>English</h5>
                    </li>
                    <li class="flex items-center justify-between">
                        <h5><i class="fa-solid fa-circle-half-stroke mr-3 text-2xl"></i>Dark Mode</h5><button
                            id="dark-switch"><i class="fa-solid fa-moon text-3xl" id="mode-btn"></i></button>
                    </li>
                    <li>
                        <h5><i class="fa-regular fa-circle-down mr-3 text-2xl"></i>Download App</h5>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</nav>

<br><br><br>

<h2 class="text-center text-4xl">Exchange Coins</h2>
    <br>
    <p class="text-center sec-font-color">Swap assets effortlessly and securely with CoinEX's self-developed algorithm
    </p>
    <br>

    <!-- Main Section -->
    <main class="main-section">
        <!-- Exchange section -->
<div class="main-container">
    <?php if (isset($_SESSION['errorMsg'])): ?>
        <div class="error">
            <?php
            echo $_SESSION['errorMsg'];
            unset($_SESSION['errorMsg']);
            ?>
        </div>
    <?php endif ?>
    <form method="POST" action="#">
    <!-- Other form fields -->
    <input type="hidden" name="exchange_rate" id="exchange_rate">
    <div class="horizontal-container">
        <div>
            <h1>Exchange Rate</h1>
            <span id="exchange-rate">Waiting for data...</span>
        </div>
        <div>
            <h1>Balance</h1>
            <span id="balance"></span>
        </div>
        <div>
            <h1>Available Amount for Coins</h1>
            <span id="available-amount"></span>
        </div>
    </div>
    <br><br>
    <!-- From coin -->
    <div class="from-coin-div">
        <label>From Coin:</label>
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
                placeholder="Enter amount" required>
            <button type="button" onclick="setMaxAmount()">Max</button>
        </div>
    </div>
    <br>
    <!-- Switch icon -->
    <img src="../../assets/images/transfer.png" alt="Exchange icon" class="w-10 m-auto cursor-pointer"
         onclick="swapCoins()">
    <br>
    <!-- To coin -->
    <div class="to-coin-div">
        <label>To Coin:</label>
        <select name="to_coin" id="to_coin" required onchange="updateCoinInfo()">
            <option value="USDT">USDT</option>
        </select>
    </div>
    <br>
    <button type="submit" class="w-[100%] h-12 bg-green-400 rounded-[50px]">Exchange</button>
</form>

   
</div>

        <!-- Your Balance -->
        <div class="coin-price">
                <h2 class="text-4xl">Current Balances</h2>
                <br>
                <!-- Table for coin price -->
                <table class="coin-table">
                    <thead>
                        <tr>
                            <th class="text-left text-2xl">Coin</th>
                            <th class="text-right text-2xl">Price</th>
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
        <h2 class="text-2xl">Your Exchange Records</h2>
        <br>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white" name="clear_record"
                onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear
                Record</button>
        </form><br>
        <table>
            <thead>
                <tr>
                    <th class="rounded-l-xl">From Coin</th>
                    <th>To Coin</th>
                    <th>From Amount</th>
                    <th>To Amount</th>
                    <th>Price</th>
                    <th class="rounded-r-xl">Exchanged At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recordsResult->num_rows > 0): ?>
                    <?php while ($record = $recordsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $record['from_coin']; ?></td>
                            <td><?php echo $record['to_coin']; ?></td>
                            <td><?php echo $record['from_amount']; ?></td>
                            <td><?php echo $record['to_amount']; ?></td>
                            <td><?php echo $record['rate']; ?></td>
                            <td><?php echo $record['transaction_date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No exchange records found.</td>
                    </tr>
                <?php endif; ?>


            </tbody>
        </table>
    </div>
    <br><br>
    <!-- Footer -->
    <footer>
        <div class="align-margin footer-sec"> <!-- to align margin -->
            <!-- Contact Icon Section -->
            <div class="contact-container w-[30%] mb-5">
                <span>
                    <img src="#" alt="LOGO">
                    <p>Making Crypto Trading Easier</p>
                </span>

                <!-- Icon Container -->
                <span class="icon-container">
                    <!-- Phone Contact -->
                    <a href="#"><img src="../../assets/images/phone-call.png" alt="Phone" class="contact-icons"></a>
                    <!-- Telegram Account -->
                    <a href="#"><img src="../../assets/images/telegram.png" alt="Telegram" class="contact-icons"></a>
                    <!-- Email -->
                    <a href="#"><img src="../../assets/images/gmail.png" alt="Email" class="contact-icons"></a>
                    <p>© 2024-2024 CoinEx.com. All rights reserved.</p>
                </span>
            </div>

            <!-- About -->
            <ul>
                <span class="gen-info-container">
                    <h2 class="text-2xl font-bold mb-5" onclick="myFunction_gsm('sec1')">About</h2><i
                        class="fa-solid fa-angle-right" id="right-arrow1"></i>
                </span>
                <span id="sec1">
                    <li>About us</li>
                    <li>Contact Us</li>
                    <li>Terms of Service</li>
                    <li>Privacy Policy</li>
                    <li>Disclaimer</li>
                    <li>About CET</li>
                    <li>Asset Security</li>
                    <li>Blog</li>
                </span>
            </ul>

            <!-- Services -->
            <ul class="">
                <span class="gen-info-container">
                    <h2 class="text-2xl font-bold mb-5" onclick="myFunction_gsm('sec2')">Services</h2>
                    <i class="fa-solid fa-angle-right" id="right-arrow2"></i>
                </span>
                <!-- First Coloum -->
                <span id="sec2">
                    <li>Market Maker</li>
                    <li>Broker</li>
                    <li>Referral Reward</li>
                    <li>Ambassador</li>
                    <li>VIP</li>
                    <li>Partner</li>
                    <li>Creator Program</li>
                </span>

                <!-- Second Coloum -->
                <!-- <span>
                    <li>Fees Standard</li>
                    <li>Apply for Token Listing</li>
                    <li>Crypto List</li>
                    <li>API File</li>
                    <li>BI Download</li>
                    <li>Proof of Reserve</li>
                    <li>Historical Market Data</li>
                </span> -->
            </ul>

            <!-- Support -->
            <ul>
                <span class="gen-info-container">
                    <h2 class="text-2xl font-bold mb-5" onclick="myFunction_gsm('sec3')">Support</h2>
                    <i class="fa-solid fa-angle-right" id="right-arrow3"></i>
                </span>
                <span id="sec3">
                    <li>Academy</li>
                    <li>Help Center</li>
                    <li>Announcements</li>
                    <li>Contact Support</li>
                    <li>ST Alert</li>
                    <li>Law Enforcement</li>
                    <li>Official Verification</li>
                </span>
            </ul>
        </div>
        <!-- Footer baseline (Second Container)-->
        <div class="footer-baseline">
            <span class="align-margin flex justify-between leading-[50px] text-white">
                <h3>2024-10-15 14:49</h3>
                <h3>24H Value : </h3>
            </span>
        </div>
    </footer>

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
let toCoin = "USDT";  // Default to coin
let currentExchangeRate = 0; // Store the current exchange rate

function connectWebSocket() {
    // Construct the symbol dynamically based on current selected coins (handle swap correctly)
    const symbol = fromCoin.toLowerCase() + toCoin.toLowerCase(); // Before swap (fromCoin -> toCoin)
    
    // If swapped, the symbol should be reversed
    const swappedSymbol = toCoin.toLowerCase() + fromCoin.toLowerCase(); // After swap (toCoin -> fromCoin)

    // Use the swappedSymbol only if a swap has occurred
    const pairSymbol = (fromCoin === 'USDT') ? swappedSymbol : symbol;

    // Set the initial "Waiting for data..." message
    document.getElementById('exchange-rate').textContent = 'Waiting for data...';
    document.getElementById('balance').textContent = 'Waiting for data...';
    document.getElementById('available-amount').textContent = 'Waiting for data...';

    if (socket) {
        socket.close();  // Close the previous WebSocket connection if it exists
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
    connectWebSocket();   // Establish WebSocket for real-time updates
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
    exchangeRateInput.value = currentExchangeRate;  // Use the latest exchange rate
}

// Add this to the form submission event
document.querySelector('form').addEventListener('submit', updateExchangeRate);

</script>
</body>

</html>