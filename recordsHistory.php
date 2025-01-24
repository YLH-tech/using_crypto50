<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();

// Include the database connection
include './php/database/db_connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}
// Number of records per page for each table
$records_per_page = 10;

// Get current page for each table (default to 1 if not set)
$current_page_orders = isset($_GET['page_orders']) ? (int)$_GET['page_orders'] : 1;
$current_page_deposits = isset($_GET['page_deposits']) ? (int)$_GET['page_deposits'] : 1;
$current_page_withdrawals = isset($_GET['page_withdrawals']) ? (int)$_GET['page_withdrawals'] : 1;
$current_page_exchanges = isset($_GET['page_exchanges']) ? (int)$_GET['page_exchanges'] : 1;

// Calculate starting records for each SQL query
$start_from_orders = ($current_page_orders - 1) * $records_per_page;
$start_from_deposits = ($current_page_deposits - 1) * $records_per_page;
$start_from_withdrawals = ($current_page_withdrawals - 1) * $records_per_page;
$start_from_exchanges = ($current_page_exchanges - 1) * $records_per_page;

// Query to fetch transaction history for orders (pagination)
$transaction_sql = "SELECT symbol, amount, starting_price, end_price, expected_pl, order_type, created_at FROM orders WHERE user_id = ? AND show_history = 'yes' LIMIT ?, ?";
$transaction_stmt = $conn->prepare($transaction_sql);
$transaction_stmt->bind_param("iii", $user_id, $start_from_orders, $records_per_page);
$transaction_stmt->execute();
$transaction_result = $transaction_stmt->get_result();

// Query to fetch deposit history (pagination)
$deposit_sql = "SELECT action, coin_type, amount, status, admin_note, timestamp FROM transactions WHERE user_id = ? AND show_history = 'yes' LIMIT ?, ?";
$deposit_stmt = $conn->prepare($deposit_sql);
$deposit_stmt->bind_param("iii", $user_id, $start_from_deposits, $records_per_page);
$deposit_stmt->execute();
$deposit_result = $deposit_stmt->get_result();

// Query to fetch withdraw history (pagination)
$withdraw_sql = "SELECT coin, amount, wallet_address, service_charge, net_amount, status, created_at FROM withdrawal_requests WHERE user_id = ? AND show_history = 'yes' LIMIT ?, ?";
$withdraw_stmt = $conn->prepare($withdraw_sql);
$withdraw_stmt->bind_param("iii", $user_id, $start_from_withdrawals, $records_per_page);
$withdraw_stmt->execute();
$withdraw_result = $withdraw_stmt->get_result();


// Query to fetch exchange history (pagination)
$exchange_sql = "SELECT from_coin, to_coin, from_amount, to_amount, rate, transaction_date FROM transactions_exchange WHERE user_id = ? AND show_history = 'yes' LIMIT ?, ?";
$exchange_stmt = $conn->prepare($exchange_sql);
$exchange_stmt->bind_param("iii", $user_id, $start_from_exchanges, $records_per_page);
$exchange_stmt->execute();
$exchange_result = $exchange_stmt->get_result();

// Pagination logic for orders table
$total_records_orders_sql = "SELECT COUNT(*) FROM orders WHERE user_id = ? AND show_history = 'yes'";
$total_records_orders_stmt = $conn->prepare($total_records_orders_sql);
$total_records_orders_stmt->bind_param("i", $user_id);
$total_records_orders_stmt->execute();
$total_records_orders_result = $total_records_orders_stmt->get_result();
$total_records_orders = $total_records_orders_result->fetch_row()[0];
$total_pages_orders = ceil($total_records_orders / $records_per_page);

// Pagination logic for deposits table
$total_records_deposits_sql = "SELECT COUNT(*) FROM transactions WHERE user_id = ? AND show_history = 'yes'";
$total_records_deposits_stmt = $conn->prepare($total_records_deposits_sql);
$total_records_deposits_stmt->bind_param("i", $user_id);
$total_records_deposits_stmt->execute();
$total_records_deposits_result = $total_records_deposits_stmt->get_result();
$total_records_deposits = $total_records_deposits_result->fetch_row()[0];
$total_pages_deposits = ceil($total_records_deposits / $records_per_page);

// Pagination logic for withdrawals table
$total_records_withdrawals_sql = "SELECT COUNT(*) FROM withdrawal_requests WHERE user_id = ? AND show_history = 'yes'";
$total_records_withdrawals_stmt = $conn->prepare($total_records_withdrawals_sql);
$total_records_withdrawals_stmt->bind_param("i", $user_id);
$total_records_withdrawals_stmt->execute();
$total_records_withdrawals_result = $total_records_withdrawals_stmt->get_result();
$total_records_withdrawals = $total_records_withdrawals_result->fetch_row()[0];
$total_pages_withdrawals = ceil($total_records_withdrawals / $records_per_page);

// Pagination logic for exchanges table
$total_records_exchanges_sql = "SELECT COUNT(*) FROM transactions_exchange WHERE user_id = ? AND show_history = 'yes'";
$total_records_exchanges_stmt = $conn->prepare($total_records_exchanges_sql);
$total_records_exchanges_stmt->bind_param("i", $user_id);
$total_records_exchanges_stmt->execute();
$total_records_exchanges_result = $total_records_exchanges_stmt->get_result();
$total_records_exchanges = $total_records_exchanges_result->fetch_row()[0];
$total_pages_exchanges = ceil($total_records_exchanges / $records_per_page);

// Create pagination links for each table
function create_pagination($current_page, $total_pages, $type) {
    $anchor = "#" . $type; // Append the section ID to the URL
    $pagination = '<div class="flex justify-center space-x-2 mt-4">';

    // First and Previous buttons
    if ($current_page > 1) {
        $pagination .= '<a href="?page_' . $type . '=1' . $anchor . '" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300" data-translate="first"></a>';
        $pagination .= '<a href="?page_' . $type . '=' . ($current_page - 1) . $anchor . '" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300" data-translate="prev"></a>';
    }

    // Page numbers
    for ($page = 1; $page <= $total_pages; $page++) {
        if ($page == $current_page) {
            $pagination .= '<span class="px-4 py-2 bg-blue-500 text-white rounded-md">' . $page . '</span>';
        } else {
            $pagination .= '<a href="?page_' . $type . '=' . $page . $anchor . '" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300">' . $page . '</a>';
        }
    }

    // Next and Last buttons
    if ($current_page < $total_pages) {
        $pagination .= '<a href="?page_' . $type . '=' . ($current_page + 1) . $anchor . '" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300" data-translate="next"></a>';
        $pagination .= '<a href="?page_' . $type . '=' . $total_pages . $anchor . '" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-300" data-translate="last"></a>';
    }

    $pagination .= '</div>';
    return $pagination;
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_order_record'])) {
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
}else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_deposit_record'])) {
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
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_withdraw_record'])) {
    // SQL query to update the show_history column in the transactions table for 'approval' action
    $update_withdraw_sql = "UPDATE transactions SET show_history = 'no' WHERE user_id = ? AND action = 'approval'";
    
    if ($stmt_withdraw = $conn->prepare($update_withdraw_sql)) {
        $stmt_withdraw->bind_param("i", $user_id);
        $stmt_withdraw->execute();
        $stmt_withdraw->close();
    }

    // Redirect to the same page to reflect the changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_exchange_record'])) {
    // SQL query to update the show_history column in the transactions_exchange table
    $update_exchange_sql = "UPDATE transactions_exchange SET show_history = 'no' WHERE user_id = ?";
    
    if ($stmt_exchange = $conn->prepare($update_exchange_sql)) {
        $stmt_exchange->bind_param("i", $user_id);
        $stmt_exchange->execute();
        $stmt_exchange->close();
    }

    // Redirect to the same page to reflect the changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/images/clock.png">
    <title data-translate="records_history">Records History</title>

    <!-- Style Links -->
    <!-- <link rel="stylesheet" href="./style/deposit_withdraw.css"> -->
    <!-- <link rel="stylesheet" href="./style/navigation.css">
    <link rel="stylesheet" href="./style/footer.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="./style/dark-mode.css">

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="./js/darkMode.js"></script>

    <style>
        @media screen and (max-width: 1300px) {
  .selected-coin {
    width: 320px;
  }
}

@media screen and (max-width: 1000px) {
  .main-section {
    display: block;
  }
  .main-container {
    width: 100%;
    margin-bottom: 20px;
  }
  .coin-price {
    width: 100%;
  }
  .coin-table {
    height: fit-content;
  }
  tbody tr {
    text-wrap: wrap;
  }
  .scrollable-area{
    display: inline-block;
  }
}
@media screen and (max-width: 660px) {
  .code-container {
    width: fit-content;
  }
  .horizontal-scroll {
    overflow-x: auto;
  }
  .horizontal-scroll table {
    width: 1500px;
    border-collapse: collapse;
    border-spacing: 0;
  }
}
@media screen and (max-width: 600px) {
  .main-container {
    padding: 20px;
  }
  .main-container ol {
    list-style-position: inside;
  }
  .showing-amount {
    width: 95%;
  }
  .input-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  .all-btn {
    background-color: var(--iconic-color--);
    padding: 5px 30px;
    color: var(--dark-font-color--);
    border-radius: 20px;
  }
}
    </style>
</head>

<body>
    <script>
        // Function to load the translation files for the selected language from both folders
    function loadTranslation(lang) {
        const historyUrl = `./translations/history/${lang}.json`; // Path to the history folder
        const mainUrl = `./translations/${lang}.json`; // Path to the main translations folder

        // Fetch translations from both folders
        return Promise.all([
            fetch(historyUrl).then(response => response.ok ? response.json() : {}),
            fetch(mainUrl).then(response => response.ok ? response.json() : {})
        ])
        .then(([historyTranslations, mainTranslations]) => {
            // Merge both translation objects, with historyTranslations taking priority
            const combinedTranslations = { ...mainTranslations, ...historyTranslations };
            applyTranslations(combinedTranslations);
            return combinedTranslations; // Return combined translations for further use
        })
        .catch(error => {
            console.error("Error loading translations:", error);
        });
    }

    // Function to apply translations to elements with the 'data-translate' attribute
    function applyTranslations(translations) {
        const elements = document.querySelectorAll("[data-translate]");
        elements.forEach(element => {
            const key = element.getAttribute("data-translate");
            if (translations[key]) {
                if (element.tagName.toLowerCase() === "input" && element.type === "submit") {
                    element.value = translations[key]; // Update button text
                } else if (element.placeholder) {
                    element.placeholder = translations[key]; // Update input placeholder
                } else {
                    element.innerHTML = translations[key]; // Update regular text content
                }
            }
        });
    }

    // Run this when the page loads
    window.onload = function () {
        // Retrieve the selected language from localStorage or default to 'en'
        const savedLanguage = localStorage.getItem('selectedLanguage') || 'en';

        // Load and apply translations for the saved language
        loadTranslation(savedLanguage);

        // Set the dropdown to match the selected language
        const languageSelector = document.getElementById('language-selector');
        if (languageSelector) {
            languageSelector.value = savedLanguage;

            // Listen for language selection changes
            languageSelector.addEventListener('change', function () {
                const selectedLanguage = this.value;
                localStorage.setItem('selectedLanguage', selectedLanguage); // Save to localStorage
                loadTranslation(selectedLanguage);
            });
        }
    };

    </script>
    <!-- Navigation Bar -->
    <nav>
        <!-- Left-side menus -->
        <div class="nav-subdiv">

            <!-- Web Logo -->
            <a href="./index.php" class="flex items-center">
                <img src="./assets/images/LOGO.png" alt="LOGO" class="web-logo w-8 mx-3">
                <h1 class="text-2xl font-bold" data-translate="nav_logo"> Bithumbnn</h1>
                <!-- <div class="resp-clickdown" id="mobile">
                    <button class="resp-clickbtn" onclick="resp_clickdown()"><i
                            class="fa-solid fa-braille ml-3 text-1xl align-middle"></i></button>
                    <div id="resp-myDropdown" class="resp-clickdown-content">
                        Academy
                        <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                            <span>
                                <h5>Academy</h5>
                                <p>Master cryptocurrency knowledge</p>
                            </span>
                        </a>
    
                        Academy
                        <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                            <span>
                                <h5>Academy</h5>
                                <p>Master cryptocurrency knowledge</p>
                            </span>
                        </a>
    
                        Academy
                        <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                            <span>
                                <h5>Academy</h5>
                                <p>Master cryptocurrency knowledge</p>
                            </span>
                        </a>
    
                        Academy
                        <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                            <span>
                                <h5>Academy</h5>
                                <p>Master cryptocurrency knowledge</p>
                            </span>
                        </a>
    
                    </div>
                </div> -->
            </a>

            <div id="non-mobile" hidden>
                <div class="dropdown">
                    <button class="dropbtn">
                        <span data-translate="nav_buy_crypto">Buy Crypto</span> <i class="fa-solid fa-caret-down drop-down-arrow"></i>
                    </button>
                    <div class="dropdown-content">

                        <!-- Deposit -->
                        <a href="./php/user_function/depo_adding.html"><img src="./assets/images/deposit.png" alt="Deposit">
                            <span>
                                <h5 data-translate="nav_deposit">Deposit</h5>
                                <p data-translate="nav_deposit_desc">Crypto deposit and your records</p>
                            </span>
                        </a>

                        <!-- Withdraw -->
                        <a href="./php/user_function/adding-withdraw.html"><img src="./assets/images/withdraw.png" alt="Withdraw">
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
                        <a href="./marketData.php"><img src="./assets/images/web-analytics.png" alt="Market Data">
                            <span>
                                <h5 data-translate="nav_market_data">Market Data</h5>
                                <p data-translate="nav_market_data_desc">Capture market opportunities</p>
                            </span>
                        </a>

                        <!-- Feed -->
                        <a href="./cryptoNews.php"><img src="./assets/images/feed.png" alt="Feed">
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
                        <a href="./php/user_function/user_balance.php"><img src="./assets/images/tax-credit.png" alt="User Balance Profile Pic">
                            <span>
                                <h5 data-translate="nav_user_profile">User Balance Profile</h5>
                                <p data-translate="nav_user_profile_desc">Your balance and your transaction history</p>
                            </span>
                        </a>

                        <!-- Coin Exchange -->
                        <a href="./php/user_function/exchange.php"><img src="./assets/images/exchange (1).png" alt="Spot Trading">
                            <span>
                                <h5 data-translate="nav_coin_exchange">Coin Exchange</h5>
                                <p data-translate="nav_coin_exchange_desc">Easily trade with any crypto combination</p>
                            </span>
                        </a>

                        <!-- Trend Trading -->
                        <a href="./php/trade_order/btcusdt.php"><img src="./assets/images/technical-analysis.png" alt="Margin Trading">
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
                        <a href="./helpCenter.php"><img src="./assets/images/help.png" alt="Help Center Pic">
                            <span>
                                <h5 data-translate="nav_help_center">Help Center</h5>
                                <p data-translate="nav_help_center_desc">Ready to help you</p>
                            </span>
                        </a>

                        <!-- Records History -->
                        <a href="./recordsHistory.php"><img src="./assets/images/clock.png" alt="History Pic">
                            <span>
                                <h5 data-translate="nav_records_history">Records History</h5>
                                <p data-translate="nav_records_history_desc">Footprints are here</p>
                            </span>
                        </a>

                        <!-- Customer Service -->
                        <a href="#"><img src="./assets/images/insurance-agent.png" alt="Customer Service">
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
                <a href="./login.php" data-translate="nav_login">Log In</a>
                <a href="./signup.php" data-translate="nav_signup"><span class="signup-btn">Sign Up</span></a>
            </div>
            <?php
            } else {
            ?>
            <a href="./profile.php"><img src="./assets/profile/default_pfp.png" alt="profile"
                    class="w-12 inline-block"></a>

            <?php
            }
            ?>

            <!-- <img src="./assets/images/avatar.png" alt="Profile" class="profile-img"> -->
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
                    <ul class="w-[90%] m-auto mobile">
                        <li>
                            <a href="./php/user_function/depo_adding.html">
                                <img src="./assets/images/deposit.png" alt="deposit logo" class="w-8">
                                <span data-translate="nav_deposit">Deposit</span>
                            </a>
                        </li>
                        <li>
                            <a href="./php/user_function/adding-withdraw.html">
                                <img src="./assets/images/withdraw.png" alt="withdraw logo" class="w-8">
                                <span data-translate="nav_withdraw">Withdraw</span>
                            </a>
                        </li>
                        <li>
                            <a href="./marketData.php">
                                <img src="./assets/images/market.png" alt="market logo" class="w-8">
                                <span data-translate="nav_market_data">Market Data</span>
                            </a>
                        </li>
                        <li>
                            <a href="./cryptoNews.php">
                                <img src="./assets/images/feed.png" alt="Feed logo" class="w-8">
                                <span data-translate="nav_feed">Feed</span>
                            </a>
                        </li>
                        <li>
                            <a href="./php/user_function/user_balance.php">
                                <img src="./assets/images/tax-credit.png" alt="User Balance Profile logo" class="w-8">
                                <span data-translate="nav_user_profile">User Balance Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="./php/user_function/exchange.php">
                                <img src="./assets/images/exchange.png" alt="Coin Exchange logo" class="w-8">
                                <span data-translate="nav_coin_exchange">Coin Exchange</span>
                            </a>
                        </li>
                        <li>
                            <a href="./php/trade_order/btcusdt.php">
                                <img src="./assets/images/technical-analysis.png" alt="Transfer" class="w-8">
                                <span data-translate="nav_trend_trading">Trend Trading</span>
                            </a>
                        </li>
                        <li>
                            <a href="./helpCenter.php">
                                <img src="./assets/images/help.png" alt="help logo" class="w-8">
                                <span data-translate="nav_help_center">Help Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="./recordsHistory.php">
                                <img src="./assets/images/clock.png" alt="clock logo" class="w-8">
                                <span data-translate="nav_records_history">Records History</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="./assets/images/insurance-agent.png" alt="Customer Service logo" class="w-8">
                                <span data-translate="nav_customer_service">Customer Service</span>
                            </a>
                        </li>
                    </ul>


                    <?php
                    if (isset($_SESSION['user_id'])) {
                    ?>
                    <a href="./php/trade_order/btcusdt.php" class="w-[100%] bg-[#78B7D0] text-center rounded-md" data-translate="nav_trade_now">Trade
                        Now</a>
                    <?php
                    } else {
                    ?>
                    <div class="click-regist">
                        <a href="./login.php" class="text-2xl" data-translate="nav_login">Log In</a>
                        <a href="./signup.php"><span class="signup-btn text-2xl" data-translate="nav_signup">Sign Up</span></a>
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
                                    window.location.href = 'download.html';
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
    <div class="align-margin">
        <!-- Transaction History -->
        <section class="transaction-history" id="orders">
            <h1 class="text-3xl" data-translate="order_history">Order History</h1>
            <form method="post">
                <button type="submit" class="bg-red-400 p-2 rounded-md " name="clear_order_record"
                onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                    Record</button>
            </form>
            <br><br>
            <div class="horizontal-scroll">
                <table>
                    <thead>
                        <tr>
                            <th class="rounded-l-md" data-translate="symbol">Symbol</th>
                            <th data-translate="amount">Amount</th>
                            <th data-translate="starting_price">Starting Price</th>
                            <th data-translate="end_price">End Price</th>
                            <th data-translate="profit_loss">Profit/Loss</th>
                            <th data-translate="order_type">Order Type</th>
                            <th class="rounded-r-md" data-translate="date">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($transaction_result->num_rows > 0) {
                            while ($row = $transaction_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["symbol"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["starting_price"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["end_price"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["expected_pl"]) . "</td>";
                                $orderTypeKey = strtolower($row["order_type"]);
                                echo "<td data-translate='" . htmlspecialchars($orderTypeKey) . "'>" . htmlspecialchars($row["order_type"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center' data-translate='no_transactions'>No transactions found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php echo create_pagination($current_page_orders, $total_pages_orders, 'orders'); ?>
        </section>
    <br>
    <hr>
    <br>
    <!-- Deposit History -->
    <section class="transaction-history" id="deposits">
        <h1 class="text-3xl" data-translate="deposit_history">Deposit History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md " name="clear_deposit_record"
            onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                Record</button>
        </form>
        <br><br>
        <div class="horizontal-scroll">
            <table>
                <thead>
                    <tr>
                        <th class="rounded-l-md" data-translate="action">Action</th>
                        <th data-translate="coin_type">Coin Type</th>
                        <th data-translate="amount">Amount</th>
                        <th data-translate="status">Status</th>
                        <th data-translate="admin_note">Admin Note</th>
                        <th class="rounded-r-md" data-translate="date">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($deposit_result->num_rows > 0) {
                        while ($row = $deposit_result->fetch_assoc()) {
                            // Determine admin_note display and ensure it is translatable
                            // $adminNote = $row["admin_note"];
                            // if (empty($adminNote)) {
                                if ($row["status"] === "pending") {
                                    $adminNote = "awaiting_approval"; // Translation key for pending
                                } elseif ($row["status"] === "approved") {
                                    $adminNote = "deposit_approved"; // Translation key for approved
                                } elseif ($row["status"] === "rejected") {
                                    $adminNote = "deposit_rejected"; // Translation key for rejected
                                }
                            // }
                
                            // Output table row
                            echo "<tr>";
                            echo "<td data-translate='" . htmlspecialchars(strtolower($row["action"])) . "'>" . htmlspecialchars($row["action"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["coin_type"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
                            echo "<td data-translate='" . htmlspecialchars(strtolower($row["status"])) . "'>" . htmlspecialchars($row["status"]) . "</td>";
                            echo "<td data-translate='" . htmlspecialchars(strtolower($adminNote)) . "'>" . htmlspecialchars($adminNote) . "</td>";
                            echo "<td>" . htmlspecialchars($row["timestamp"]) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center' data-translate='no_deposits'>No deposits found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php echo create_pagination($current_page_deposits, $total_pages_deposits, 'deposits'); ?>
    </section>
    <br>
    <hr>
    <br>
    <!-- Withdraw History -->
    <section class="transaction-history" id="withdrawals">
        <h1 class="text-3xl" data-translate="withdraw_history">Withdraw History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md " name="clear_withdraw_record"
            onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                Record</button>
        </form>
        <br>
        <br>
       <div class="horizontal-scroll"> <table>
        <thead>
            <tr>
                <th class="rounded-l-md" data-translate="coin_type">Coin Type</th>
                <th data-translate="wallet_address">Wallet Address</th>
                <th data-translate="amount">Amount</th>
                <th data-translate="service_charge">Service Charge</th>
                <th data-translate="net_amount">Net Amount</th>
                <th data-translate="admin_note">Admin Note</th>
                <th class="rounded-r-md" data-translate="date">Date</th>
            </tr>
        </thead>
        <tbody>
                    <?php
                    if ($withdraw_result->num_rows > 0) {
                        while ($row = $withdraw_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["coin"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["wallet_address"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["amount"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["service_charge"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["net_amount"]) . "</td>";
                            echo "<td data-translate='" . htmlspecialchars($row["status"]) . "'>" . htmlspecialchars($row["status"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center' data-translate='no_withdrawals'>No withdrawals found.</td></tr>";
                    }
                    ?>
                </tbody>
    </table></div>
    <?php echo create_pagination($current_page_withdrawals, $total_pages_withdrawals, 'withdrawals'); ?>
    </section>
    <br>
    <hr>
    <br>
    <!-- Exchange History -->
    <section class="transaction-history" id="exchanges">
        <h1 class="text-3xl" data-translate="exchange_history">Exchange History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md " name="clear_exchange_record"
            onclick="return confirmClearRecord();" data-translate="clear_record">Clear
                Record</button>
        </form>
        <br><br>
        <div class="horizontal-scroll">
            <table>
                <thead>
                    <tr>
                        <th class="rounded-l-md" data-translate="from_coin">From Coin</th>
                        <th data-translate="to_coin">To Coin</th>
                        <th data-translate="from_amount">From Amount</th>
                        <th data-translate="to_amount">To Amount</th>
                        <th data-translate="rate">Rate</th>
                        <th class="rounded-r-md" data-translate="date">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($exchange_result->num_rows > 0) { ?>
                        <?php while ($row = $exchange_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['from_coin']); ?></td>
                                <td><?php echo htmlspecialchars($row['to_coin']); ?></td>
                                <td><?php echo htmlspecialchars($row['from_amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['to_amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['rate']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                        <tr><td colspan="6" class="text-center" data-translate="no_exchange_history">No exchange history available.</td></tr>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php echo create_pagination($current_page_exchanges, $total_pages_exchanges, 'exchanges'); ?>
    </section>

    </div>

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

    <!-- ===================================JavaScripts=============================== -->

    <!-- Side menu icon clicking -->
    <script src="./js/dropdownScript.js"></script>

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