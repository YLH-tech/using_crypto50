<?php
session_start();
include 'db.php';
include '../database/db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Get user's USDT balance using PDO
$user_id = $_SESSION['user_id'];
$query = "SELECT usdt FROM user_balances WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_balance = $stmt->fetch(PDO::FETCH_ASSOC);
$available_usdt = $user_balance['usdt'] ?? 0.00;


$query = "SELECT allow FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
$allow = $user_data['allow'] ?? 'off'; // Default to 'off' if not set

// Hide transaction history (instead of deleting)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_deposit_record'])) {
    // SQL query to update the show_history column in the orders table
    $update_order_sql = "UPDATE orders SET show_history = 'no' WHERE user_id = ?";
    
    if ($stmt_order = $conn->prepare($update_order_sql)) {
        $stmt_order->bind_param("i", $user_id);
        $stmt_order->execute();
        $stmt_order->close();
    }

    // Redirect to the same page to reflect the changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch orders with pagination
if (isset($_GET['fetch_orders'])) {
    // Set the number of records per page
    $recordsPerPage = 20;

    // Get the current page number, default to 1 if not provided
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculate the starting record based on the current page
    $startRecord = ($page - 1) * $recordsPerPage;

    // Prepare the query with LIMIT for pagination
    $query = "SELECT symbol, amount, starting_price, end_price, expected_pl, order_type, created_at 
              FROM orders WHERE user_id = :user_id AND show_history = 'yes' ORDER BY created_at DESC LIMIT :start, :limit";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':start', $startRecord, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total pages
    $countQuery = "SELECT COUNT(*) FROM orders WHERE user_id = :user_id";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Return orders and pagination information
    echo json_encode([
        'orders' => $orders,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bithumbnn</title>
    <!-- Style Links -->
    <!-- <link rel="stylesheet" href="../../style/deposit_withdraw.css">
    <link rel="stylesheet" href="chart.css">
    <link rel="stylesheet" href="../../style/pagination.css">
    <link rel="stylesheet" href="../../style/navigation.css">
    <link rel="stylesheet" href="../../style/footer.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="./darkMode.js">
    <!-- <script src="lightweight-charts.standalone.production.js"></script> -->

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- JQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->


    <script src="./darkMode.js"></script>
    <script>
        // Function to load the TradingView script
        function loadTradingView() {
            return new Promise((resolve, reject) => {
                if (document.getElementById('tradingview-script')) {
                    resolve(); // Script is already loaded
                    return;
                }
                const script = document.createElement('script');
                script.id = 'tradingview-script';
                script.src = "tv.js";
                script.onload = () => resolve();
                script.onerror = () => reject(new Error('Failed to load TradingView script.'));
                document.body.appendChild(script);
            });
        }

        // Function to initialize the TradingView widget
        function loadWidget(language = 'en') {
            // Map your language codes to TradingView's locale codes
            const languageMap = {
                'en': 'en',
                'es': 'es',
                'fr': 'fr',
                'ja': 'ja',
                'zh': 'zh_CN', // Simplified Chinese
            };

            const selectedLanguage = languageMap[language] || 'en'; // Default to 'en' if not mapped

            // Clear the container before loading the new widget
            const container = document.getElementById('tradingview-chart');
            container.innerHTML = ''; // Clear the previous widget

            new TradingView.widget({
                "autosize": true,
                "symbol": "BINANCE:BNBUSDT",
                "interval": "1",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": selectedLanguage, // Use the mapped language
                "hide_legend": true,
                "hide_side_toolbar": false,
                "allow_symbol_change": false,
                "save_image": false,
                "details": true,
                "calendar": false,
                "container_id": "tradingview-chart"
            });
        }

        // Event listener for language change
        function handleLanguageChange(newLanguage) {
            // Update localStorage with the new language
            localStorage.setItem('selectedLanguage', newLanguage);

            // Reload the widget with the new language
            loadWidget(newLanguage);
        }

        // Initialize on page load
        window.addEventListener('load', () => {
            loadTradingView()
                .then(() => {
                    const selectedLanguage = localStorage.getItem('selectedLanguage') || 'en';
                    loadWidget(selectedLanguage);

                    // Example: Add a listener to a language dropdown (customize for your implementation)
                    const languageDropdown = document.getElementById('language-selector');
                    if (languageDropdown) {
                        languageDropdown.addEventListener('change', (event) => {
                            handleLanguageChange(event.target.value);
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('tradingview-chart').innerText = 'Failed to load chart. Please try again later.';
                });
        });
    </script>
</head>

<body>
    <script src="../../js/translate_trade.js"></script>
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
    <br><br><br>
    <header>
        <h1>BNBUSDT</h1>
        <div>
        <span id="price-change-info" data-translate="price-change-info">24h Change: 0.00%</span>
        </div>
    </header>

    <div class="container">
        <aside class="market-list">
            <ul id="market-list">
                <li data-symbol="btcusdt">
                    <strong>BTCUSDT</strong> <span class="price">$0.00</span> <span class="change">(0.00%)</span>
                </li>
                <li data-symbol="ethusdt">
                    <strong>ETHUSDT</strong> <span class="price">$0.00</span> <span class="change">(0.00%)</span>
                </li>
            </ul>
        </aside>

        <main class="chart-area">
            <div class="tradingview-widget-container" style="height:100%; width:100%;">
                <div id="tradingview-chart" style="height: 720px;"></div>
            </div>
            <div class="scrollable-area">
                <div class="trade-columns">
                    <div class="buy-column">
                        <p><span data-translate="available_balance">Available Balance(USDT): </span>&nbsp<span id="available-usdt"><?php echo number_format($available_usdt, 6); ?></span></p>
                        <span id="buy-price" data-translate="Loading">Loading...</span><br><br>  
                        <!-- <div id="maxBuyBTC" style="color:grey;">Max Buy BTC: 0.000000</div> -->
                        <form id="buy-form">
                        <label for="buy-amount"><span data-translate="amount">Amount</span> (BNB):</label>
                            <input type="number" id="buy-amount" placeholder="Quantity you want to buy" data-translate-placeholder="buy_placeholder" min="0" step="0.000001" oninput="validateAmount('buy')">
                            <div data-translate="select_period">select period:</div>
                            <table class="option-table">
                                <tr>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_30s">30s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_60s">60s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_120s">120s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_300s">300s</button></td>
                                </tr>
                                <tr>
                                    <td><span class="percent-option">40%</span></td>
                                    <td><span class="percent-option">50%</span></td>
                                    <td><span class="percent-option">70%</span></td>
                                    <td><span class="percent-option">100%</span></td>
                                </tr>
                            </table>

                            <button type="submit" id="buy-button"><span data-translate="buy">Buy</span> BNB</button>
                        </form>
                    </div>

                    <div class="sell-column">
                        <p><span data-translate="available_balance">Available Balance(USDT): </span>&nbsp<span class="available-usdt"><?php echo number_format($available_usdt, 6); ?></span></p>
                        <span id="sell-price" data-translate="Loading">Loading...</span><br><br>
                        <!-- <div id="maxSellBTC" style="color:grey;">Max Sell BTC: 0.000000</div> -->
                        <form id="sell-form">

                        <label for="sell-amount"><span data-translate="amount">Amount</span> (BNB):</label>
                            <input type="number" id="sell-amount" placeholder="Quantity you want to sell" data-translate-placeholder="sell_placeholder" min="0" step="0.000001" oninput="validateAmount('sell')">
                            <div data-translate="select_period">select period:</div>
                            <table class="option-table">
                                <tr>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_30s">30s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_60s">60s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_120s">120s</button></td>
                                    <td><button type="button" class="time-option" onclick="selectTimeOption(this)" data-translate="period_300s">300s</button></td>
                                </tr>
                                <tr>
                                    <td><span class="percent-option">40%</span></td>
                                    <td><span class="percent-option">50%</span></td>
                                    <td><span class="percent-option">70%</span></td>
                                    <td><span class="percent-option">100%</span></td>
                                </tr>
                            </table>

                            <button type="submit" id="sell-button"><span data-translate="sell">Sell</span> BNB</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <aside class="order-book">
            <table id="orderTable">
                <thead>
                    <tr>
                        <th data-translate="price_usdt">Price (USDT)</th>
                        <th><span data-translate="amount">Amount</span> (BNB)</th>
                        <th data-translate="total_usdt">Total (USDT)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </aside>
    </div>

    <!-- Transaction History Section -->
    <section class="transaction-history">
        <h1 class="text-3xl" data-translate="order_records">Order records</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md" name="clear_deposit_record"
                onclick="confirmClearRecord(event);" data-translate="clear_record">
                Clear Record
            </button>
        </form>
        <br>
        <div class="scrollable-area1">
        <table>
            <thead>
                <tr>
                    <th class="rounded-l-md" data-translate="symbol">Symbol</th>
                    <th data-translate="amount">Amount</th>
                    <th data-translate="starting_price">Starting Price</th>
                    <th data-translate="end_price">End Price</th>
                    <th data-translate="profit_loss">Profit/Loss</th>
                    <th data-translate="type">Order Type</th>
                    <th class="rounded-r-md" data-translate="date">Date</th>
                </tr>
            <tbody id="order-history">
                <!-- Dynamic rows will be added here -->
            </tbody>
        </table>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination" class="pagination-controls">
            <!-- Pagination buttons will appear here -->
        </div>
    </section>

    <!-- Overlay for Order Details -->
    <div id="order-details-overlay">
        <div class="overlay-content">
            <!-- Content will be dynamically inserted here by JavaScript -->
        </div>
    </div>
    <!-- Overlay for Order Confirmation -->
    <div id="order-confirmation-overlay" style="display: none;">
        <div class="overlay-content">
            <h2>Order Confirmation</h2>
            <p>Your order has been successfully completed!</p>
            <button id="close-confirmation">Close</button>
        </div>
    </div>

    <br>
    <hr>
                        <br>
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
    
    <script>
    // Global variable to store the current page
    let currentPage = 1;

    // Fetch and update the table dynamically with pagination
    async function fetchOrderHistory(page = 1) {
        try {
            const response = await fetch(`bnbusdt.php?fetch_orders=1&page=${page}`);
            const data = await response.json();
            const tableBody = document.getElementById('order-history');
            tableBody.innerHTML = ''; // Clear existing rows

            // Fetch translations for 'Buy' and 'Sell'
            const buyText = await getTranslatedText('buy');
            const sellText = await getTranslatedText('sell');

            // Populate the table with order data
            data.orders.forEach(order => {
                // Translate 'order_type'
                const translatedOrderType = order.order_type === 'Buy' ? buyText : sellText;

                const row = `
                    <tr>
                        <td>${order.symbol}</td>
                        <td>${order.amount}</td>
                        <td>${order.starting_price}</td>
                        <td>${order.end_price}</td>
                        <td>${order.expected_pl}</td>
                        <td>${translatedOrderType}</td>
                        <td>${order.created_at}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            // Update the pagination controls
            updatePagination(data.totalPages, data.currentPage);
        } catch (error) {
            console.error('Error fetching order history:', error);
        }
    }


    // Update pagination controls (Next, Previous, and Page Numbers)
    async function updatePagination(totalPages, currentPage) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        // Get translated text for Previous and Next
        const previousText = await getTranslatedText('previous');
        const nextText = await getTranslatedText('next');

        // Previous Button
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.textContent = previousText; // Use translated text
            prevBtn.onclick = () => changePage(currentPage - 1);
            pagination.appendChild(prevBtn);
        }

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = i === currentPage ? 'active' : ''; // Highlight current page
            pageBtn.onclick = () => changePage(i);
            pagination.appendChild(pageBtn);
        }

        // Next Button
        if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.textContent = nextText; // Use translated text
            nextBtn.onclick = () => changePage(currentPage + 1);
            pagination.appendChild(nextBtn);
        }
    }

    // Change the page when a page number or next/previous is clicked
    function changePage(page) {
        currentPage = page;
        fetchOrderHistory(page);
    }

    // Call this function to fetch and display orders on page load
    fetchOrderHistory(currentPage);

    </script>

    <!-- This script is for current_price and Max buy/sell BTC in trade columns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceElement = document.getElementById('buy-price');
            const sellPriceElement = document.getElementById('sell-price');

            // Load the translations
            const savedLanguage = localStorage.getItem('selectedLanguage') || 'en';
            let translations = {};
            loadTranslation(savedLanguage).then(data => translations = data);

            function connectWebSocket() {
                const ws = new WebSocket('wss://stream.binance.com:9443/ws/bnbusdt@trade'); // Binance BTC/USDT trade stream

                ws.onmessage = event => {
                    const data = JSON.parse(event.data);
                    const currentPrice = parseFloat(data.p).toFixed(2);

                    // Update price displays
                    priceElement.innerText = `${translations["price_usdt"] || "Price (USDT):"} ${currentPrice}`;;
                    sellPriceElement.innerText = `${translations["price_usdt"] || "Price (USDT):"} ${currentPrice}`;

                };

                ws.onerror = error => {
                    console.error('WebSocket Error:', error);
                    setTimeout(connectWebSocket, 5000); // Retry on error
                };

                ws.onclose = () => {
                    console.log('WebSocket disconnected. Reconnecting...');
                    setTimeout(connectWebSocket, 5000); // Reconnect after disconnect
                };
            }

            connectWebSocket();
        });
    </script>
    <!-- This script is for all trade order functions -->
    <script>
        const userAllow = <?= json_encode($allow); ?>;

        let selectedTimeInterval = null;

        function selectTimeOption(button) {
            const form = button.closest('form');
            const buttonId = form.id === 'buy-form' ? 'buy-button' : 'sell-button';

            // Deselect previously selected button in this row
            form.querySelectorAll('.time-option').forEach(opt => opt.classList.remove('selected'));

            // Select the clicked button and store time interval
            button.classList.add('selected');
            selectedTimeInterval = parseInt(button.innerText) * 1000; // Convert to milliseconds

            // Enable the submit button for the form
            document.getElementById(buttonId).disabled = false;
        }

        async function validateForm(event, formId) {
            event.preventDefault(); // Prevent form submission initially

            const form = document.getElementById(formId);
            const selectedOption = form.querySelector('.time-option.selected');
            const amountInput = form.querySelector('input[type="number"]');
            const amount = parseFloat(amountInput.value) || 0;
            const priceText = document.getElementById(formId === 'buy-form' ? 'buy-price' : 'sell-price').innerText;
            const priceMatch = priceText.match(/[\d.]+/);  // This matches any sequence of digits and dots in the text
            const price = priceMatch ? parseFloat(priceMatch[0]) : NaN;
            const availableBalance = <?= json_encode($available_usdt); ?>;

            // Translate alert messages
            const alertMessages = {
                priceNotLoaded: await getTranslatedText('price_not_loaded'),
                insufficientBalance: await getTranslatedText('insufficient_balance'),
                amountRequired: await getTranslatedText('amount_required'),
                timeIntervalRequired: await getTranslatedText('time_interval_required')
            };

            // Show alert if price is not loaded
            if (isNaN(price) || price <= 0) {
                alert(alertMessages.priceNotLoaded);
                return; // Stop further execution after alert
            }

            // Show alert if insufficient balance
            if (amount > availableBalance) {
                alert(alertMessages.insufficientBalance);
                return; // Stop further execution after alert
            }

            // Show alert if amount is empty
            if (!amountInput.value.trim()) {
                alert(alertMessages.amountRequired);
                return; // Stop further execution after alert
            }

            // Show alert if no time interval is selected
            if (!selectedOption) {
                alert(alertMessages.timeIntervalRequired);
                return; // Stop further execution after alert
            }

            // All checks passed, proceed with countdown overlay
            showCountdownOverlay(formId, amount); // This will run only if all validations pass
        }


        function showCountdownOverlay(formId, amount) {
            const orderDirection = formId === 'buy-form' ? 'Buy' : 'Sell';
            const symbol = 'BNBUSDT';

            // Load translation for dynamic text
            const selectedLanguage = localStorage.getItem('selectedLanguage') || 'en';
            let translations = currentTranslations; // Use cached translations

            // Log the translations to debug
            // console.log(translations);

            // Fetch and format the current price
            const priceText = document.getElementById(formId === 'buy-form' ? 'buy-price' : 'sell-price').innerText;

            // Extract numeric value from priceText using regex
            const priceMatch = priceText.match(/[\d.]+/);  // This matches any sequence of digits and dots in the text
            const currentPrice = priceMatch ? parseFloat(priceMatch[0]).toFixed(2) : '0.00';

            // Populate overlay with order information and formatted price
            const overlay = document.getElementById('order-details-overlay');
            overlay.classList.add('active'); // Make overlay visible
            overlay.querySelector('.overlay-content').innerHTML = `
                <h2>${translations[orderDirection.toLowerCase() + "_order"] || orderDirection + " Order"}</h2>
                <p>${translations["symbol"] || "Symbol"}: ${symbol}</p>
                <p>${translations["amount"] || "Amount"}: ${amount} BNB</p>
                <p>${translations["starting_price"] || "Starting Price"}: $${currentPrice}</p>
                <p id="realTimePrice">${translations["current_price"] || "Current Price"}: $${currentPrice}</p>
                <p id="expectedPL">${translations["expected_pl"] || "Expected P/L"}: $0.00</p>
                <canvas id="countdown-timer-circle" width="180" height="180"></canvas>
            `;

            startCountdown(overlay, selectedTimeInterval, currentPrice, amount, orderDirection, translations); 
        }


        function startCountdown(overlay, timeRemaining, startPrice, amount, orderDirection, translations) {
            const countdownCanvas = document.getElementById('countdown-timer-circle');
            const ctx = countdownCanvas.getContext('2d');
            const radius = countdownCanvas.width / 2;
            const startAngle = -Math.PI / 2; // Start from the top
            const fullTime = timeRemaining; // Store initial countdown time

            const priceElement = overlay.querySelector('#realTimePrice');
            const plElement = overlay.querySelector('#expectedPL');

            const percentages = {
                30000: 0.40,
                60000: 0.50,
                120000: 0.70,
                300000: 1.00
            };
            const selectedPercentage = percentages[selectedTimeInterval] || 1;

            const userAllow = <?= json_encode($allow); ?>;


            const ws = new WebSocket('wss://stream.binance.com:9443/ws/bnbusdt@trade');
            ws.onmessage = event => {
                const data = JSON.parse(event.data);
                const currentPrice = parseFloat(data.p).toFixed(2);
                priceElement.innerText = `${translations["current_price"] || "Current Price"}: $${currentPrice}`;

                let profitLoss;
                if (userAllow === 'on') {
                    profitLoss = (amount + (amount * selectedPercentage)).toFixed(6);
                } else {
                    profitLoss = (-amount).toFixed(6);
                }
                plElement.innerText = `${translations["expected_pl"] || "Expected P/L"}: $${profitLoss}`;
                plElement.style.color = profitLoss >= 0 ? 'limegreen' : 'red';
            };

            function drawCountdownCircle(timeLeft) {
                const progress = timeLeft / fullTime;
                const endAngle = startAngle + 2 * Math.PI * (1 - progress);

                // Clear the canvas
                ctx.clearRect(0, 0, countdownCanvas.width, countdownCanvas.height);

                // Draw gradient for progress circle
                const gradient = ctx.createLinearGradient(0, 0, countdownCanvas.width, 0);
                gradient.addColorStop(0, '#007bff');
                gradient.addColorStop(1, '#00c9ff');

                // Draw background circle with shadow
                ctx.beginPath();
                ctx.arc(radius, radius, radius - 10, 0, 2 * Math.PI);
                ctx.lineWidth = 12;
                ctx.strokeStyle = '#333';
                ctx.shadowBlur = 20;
                ctx.shadowColor = 'rgba(0, 123, 255, 0.3)';
                ctx.stroke();
                ctx.shadowBlur = 0;  // Reset shadow

                // Draw progress circle with gradient
                ctx.beginPath();
                ctx.arc(radius, radius, radius - 10, startAngle, endAngle, false);
                ctx.strokeStyle = gradient;
                ctx.lineWidth = 12;
                ctx.stroke();

                // Translate and draw remaining time in the center with glow
                const secondText = translations["seconds"] || "s";  // Default to "s" if no translation is provided
                ctx.fillStyle = '#ffffff';
                ctx.font = '24px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.shadowColor = 'rgba(0, 123, 255, 0.6)';
                ctx.shadowBlur = 10;
                ctx.fillText((timeLeft / 1000).toFixed(0) + secondText, radius, radius);
                ctx.shadowBlur = 0;  // Reset shadow
            }

            const countdownInterval = setInterval(() => {
                timeRemaining -= 1000;
                drawCountdownCircle(timeRemaining);

                if (timeRemaining <= 0) {
                    clearInterval(countdownInterval);
                    ws.close();
                    overlay.classList.remove('active'); // Hide the order details overlay
                    showOrderConfirmation(symbol, amount, startPrice, orderDirection, translations); // Pass translations to showOrderConfirmation
                    saveOrderHistory(startPrice, amount, parseFloat(plElement.innerText.split('$')[1]));
                }
            }, 1000);
        }

        function showOrderConfirmation(symbol, amount, startPrice, orderDirection) {
            const confirmationOverlay = document.getElementById('order-confirmation-overlay');

            // Extract the raw current price without relying on translations
            const priceElement = document.getElementById('realTimePrice');
            const endPriceMatch = priceElement.textContent.match(/[\d.]+/); // Extract numeric value from text
            const endPrice = endPriceMatch ? parseFloat(endPriceMatch[0]).toFixed(2) : '0.00';

            const selectedPercentage = {
                30000: 0.40,
                60000: 0.50,
                120000: 0.70,
                300000: 1.00
            }[selectedTimeInterval] || 1;

            let profitLoss;
            if (userAllow === 'on') {
                profitLoss = (amount + (amount * selectedPercentage)).toFixed(6);
            } else {
                profitLoss = (-amount).toFixed(6);
            }

            // Load translation for dynamic text
            const selectedLanguage = localStorage.getItem('selectedLanguage') || 'en';
            let translations = {};
            loadTranslation(selectedLanguage).then(data => {
                translations = data;

                confirmationOverlay.querySelector('.overlay-content').innerHTML = `
                    <h2>${translations["order_confirmation"]}</h2>
                    <p>${translations["order_success"]}</p>
                    <p>${translations["symbol"]}: ${symbol}</p>
                    <p>${translations["amount"]}: ${amount} BNB</p>
                    <p>${translations["starting_price"]}: $${startPrice}</p>
                    <p>${translations["end_price"] || "End Price"}: $${endPrice}</p>
                    <p>${translations["profit_loss"] || "Profit/Loss"}: $${profitLoss}</p>
                    <button id="close-confirmation">${translations["close"]}</button>
                `;

                confirmationOverlay.style.display = 'flex';

                saveOrderToDatabase(symbol, amount, startPrice, endPrice, profitLoss, orderDirection);

                document.getElementById('close-confirmation').onclick = () => {
                    confirmationOverlay.style.display = 'none';
                };
            });
        }

        function saveOrderToDatabase(symbol, amount, startingPrice, endPrice, expectedPL, orderDirection) {
            const orderData = {
                symbol: symbol,
                amount: amount,
                starting_price: startingPrice,
                end_price: endPrice,
                expected_pl: expectedPL,
                order_type: orderDirection // Pass the order type
            };

            fetch('save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Order saved successfully:', data.message);
                    updateUserBalance(expectedPL);
                    fetchOrderHistory();
                } else {
                    console.error('Error saving order:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }


        function updateUserBalance(profitLoss) {
            fetch('update_balance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ profit_loss: profitLoss }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('User balance updated successfully:', data.message);
                    refreshUserBalance(); // Call to fetch the updated balance
                } else {
                    console.error('Error updating balance:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }

        function refreshUserBalance() {
            // Fetch the updated balance from the server
            fetch('get_balance.php') // This PHP file should return the updated balance
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update the displayed available USDT in both buy and sell columns
                        const updatedBalance = parseFloat(data.balance.replace(/,/g, '')); // Remove commas for proper number formatting

                        // Update the available-usdt element
                        document.getElementById('available-usdt').innerText = updatedBalance.toFixed(6);
                        document.querySelector('.available-usdt').innerText = updatedBalance.toFixed(6);
                    } else {
                        console.error('Error fetching updated balance:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching balance:', error);
                });
        }

    
        function saveOrderHistory(startPrice, amount, profitLoss) {
                // Placeholder function to save order to history
                console.log("Saving order history:", {
                    startPrice,
                    amount,
                    profitLoss
                });
                // Implement actual saving to database if needed
            }


            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('buy-form').addEventListener('submit', function(event) {
                    validateForm(event, 'buy-form');
                });

                document.getElementById('sell-form').addEventListener('submit', function(event) {
                    validateForm(event, 'sell-form');
                });
            });
    </script>

    <!-- This script is for left-side(Market list), right-side(ordr book) -->
    <script>
        const marketList = [
            'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT',
            'ADAUSDT', 'TRXUSDT', 'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT',
            'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 'EOSUSDT', 'ARBUSDT',
            'APTUSDT', 'TONUSDT'
        ];

        let symbol = 'BNBUSDT'; // Initially set to BTCUSDT
        let interval = '1m';
        let binanceSocket = null;

        async function loadMarketData() {
            document.getElementById('market-list').innerHTML = ''; // Clear existing list
            const uniqueMarkets = [...new Set(marketList)]; // Ensure unique symbols
            for (const coin of uniqueMarkets) {
                try {
                    const tickerRes = await fetch(`https://api.binance.com/api/v3/ticker/24hr?symbol=${coin}`);
                    const tickerData = await tickerRes.json();
                    const priceChangePercent = parseFloat(tickerData.priceChangePercent).toFixed(2);
                    const price = parseFloat(tickerData.lastPrice).toFixed(2);

                    const li = document.createElement('li');
                    li.dataset.symbol = coin.toLowerCase(); // Add a data attribute for the symbol
                    li.innerHTML = `<strong>${coin}</strong> <span class="price">$${price}</span> <span class="change">(${priceChangePercent}%)</span>`; // Separate elements for price and change

                    // Add a click event to navigate to a specific page
                    li.addEventListener('click', () => {
                        window.location.href = `${coin.toLowerCase()}.php`; // Modify this line with your desired link format
                    });

                    document.getElementById('market-list').appendChild(li);
                } catch (error) {
                    console.error('Error fetching market data:', error);
                }
            }
        }


        // Start real-time WebSocket updates for the selected market symbol
        function startWebSocket(symbol, interval) {
            if (binanceSocket) {
                binanceSocket.close();
            }

            // // Kline WebSocket for candlestick updates
            const socketUrl = `wss://stream.binance.com:9443/ws/${symbol}@kline_${interval}`;
            binanceSocket = new WebSocket(socketUrl);



            // Ticker WebSocket for 24-hour change percentage for all markets
            const tickerSocketUrl = `wss://stream.binance.com:9443/ws/${symbol}@ticker`;
            const tickerSocket = new WebSocket(tickerSocketUrl);

            tickerSocket.onmessage = function (event) {
                const tickerData = JSON.parse(event.data);
                const priceChangePercent = parseFloat(tickerData.P); // Use 'P' for the percentage change
                const newPrice = parseFloat(tickerData.c).toFixed(2); // Latest price from ticker data
                const tickerSymbol = tickerData.s.toLowerCase(); // Symbol in lowercase

                // Update the header 24h change for BTCUSDT
                if (tickerSymbol === 'bnbusdt') {
                    const headerChangeElement = document.getElementById('price-change-info');
                    if (headerChangeElement) {
                        getTranslatedText('price-change-info')
                            .then(translatedText => {
                                headerChangeElement.innerHTML = `${translatedText}: <strong>${priceChangePercent.toFixed(2)}%</strong>`;
                                const valueElement = headerChangeElement.querySelector('strong');
                                valueElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
                            })
                            .catch(err => console.error("Error fetching translation:", err));
                    }
                }

                // Update the left sidebar market list for the specific symbol
                const listItem = document.querySelector(`li[data-symbol="${tickerSymbol}"]`);
                if (listItem) {
                    const priceElement = listItem.querySelector('.price');
                    priceElement.textContent = `$${newPrice}`;

                    const changeElement = listItem.querySelector('.change');
                    changeElement.textContent = `(${priceChangePercent.toFixed(2)}%)`;

                    // Apply color based on the price change percentage
                    priceElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
                    changeElement.style.color = priceChangePercent > 0 ? '#28a745' : priceChangePercent < 0 ? '#dc3545' : '#000';
                }
            };

            tickerSocket.onerror = function (error) {
                console.error("Ticker WebSocket error:", error);
            };
        }


        // Initialize WebSocket for all markets
        marketList.forEach(coin => {
            startWebSocket(coin.toLowerCase(), interval);
        });

        // Initial data load
        loadMarketData();
        // loadHistoricalData(symbol, interval);
        // startWebSocket(symbol, interval);

        // Resize chart on window resize
        // window.addEventListener('resize', () => {
        //     chart.resize(document.getElementById('chart').clientWidth, 500);
        // });

        const orderTableBody = document.querySelector('#orderTable tbody');

        // Create a row for the current price
        const currentPriceRow = document.createElement('tr');
        currentPriceRow.className = 'current-price-row'; // Add a class for styling
        currentPriceRow.innerHTML = `
        <td colspan="3" style="font-weight: bold; text-align: left; font-size: 16px;">
            <span id="currentPrice" style="font-size: 16px;"></span>
        </td>
    `;

        // Connecting to Binance WebSocket for BNBUSDT order book
        const socket = new WebSocket('wss://stream.binance.com:9443/ws/bnbusdt@depth');

        let lastPrice = 0;

        // Function to fill the order table with the latest data
        function fillOrderTable(sellOrders, buyOrders) {
            // Clear the table body except for the current price row
            orderTableBody.innerHTML = '';

            // Fill the sell orders (asks) with price only
            for (let i = 0; i < 10; i++) { // Updated to 10
                const order = sellOrders[i] || [0, 0]; // Fallback to zero
                const price = parseFloat(order[0]);
                const amount = parseFloat(order[1]);
                const total = price * amount;

                const row = document.createElement('tr');
                row.innerHTML = `
                <td style="color: red; font-weight: bold; font-size: 12px;">${price.toFixed(2)}</td>
                <td style="color: grey; font-weight: bold; font-size: 12px;">${amount.toFixed(5)}</td>
                <td style="color: grey; font-weight: bold; font-size: 12px;">${(total / 1000).toFixed(2)}K</td>
            `;
                orderTableBody.appendChild(row);
            }

            // Insert the current price row between sell and buy orders
            orderTableBody.appendChild(currentPriceRow);

            // Fill the buy orders (bids) with price only
            for (let i = 0; i < 10; i++) { // Updated to 10
                const order = buyOrders[i] || [0, 0]; // Fallback to zero
                const price = parseFloat(order[0]);
                const amount = parseFloat(order[1]);
                const total = price * amount;

                const row = document.createElement('tr');
                row.innerHTML = `
                <td style="color: green; font-weight: bold; font-size: 12px;">${price.toFixed(2)}</td>
                <td style="color: grey; font-weight: bold; font-size: 12px;">${amount.toFixed(5)}</td>
                <td style="color: grey; font-weight: bold; font-size: 12px;">${(total / 1000).toFixed(2)}K</td>
            `;
                orderTableBody.appendChild(row);
            }
        }

        socket.onopen = function () {
            console.log("WebSocket connection established");
        };

        socket.onmessage = function (event) {
            const data = JSON.parse(event.data);
            console.log("Parsed data:", data); // Log the parsed data to verify its structure

            // Get the top 10 sell orders (asks)
            const sellOrders = data.a ? data.a.slice(0, 10) : []; // Updated to 10

            // Get the top 10 buy orders (bids)
            const buyOrders = data.b ? data.b.slice(0, 10) : []; // Updated to 10

            // Call function to fill the order table
            fillOrderTable(sellOrders, buyOrders);

            // Update the current price
            const topSellPrice = sellOrders.length > 0 ? parseFloat(sellOrders[0][0]) : null;
            const topBuyPrice = buyOrders.length > 0 ? parseFloat(buyOrders[0][0]) : null;

            if (topSellPrice !== null && topBuyPrice !== null) {
                const currentPrice = (topSellPrice + topBuyPrice) / 2; // Average between top sell and buy
                document.getElementById('currentPrice').textContent = `${currentPrice.toFixed(2)}`;

                if (currentPrice > lastPrice) {
                    document.getElementById('currentPrice').style.color = 'green'; // Price increased (buy)
                } else if (currentPrice < lastPrice) {
                    document.getElementById('currentPrice').style.color = 'red'; // Price decreased (sell)
                }
                lastPrice = currentPrice; // Store the last price for comparison
            }
        };

        socket.onerror = function (error) {
            console.error("WebSocket error:", error);
        };

        socket.onclose = function () {
            console.log("WebSocket connection closed");
        };


    </script>
</body>

</html>