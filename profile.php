<?php
session_start();
require "./php/database/db_connection.php";
$userID = $_SESSION["user_id"];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $userID"));

$name = $user['username'];
$email = $user['email'];
// Set the profile image to always be the default profile picture
$profile = 'default_pfp.png';

// Get the number of approved deposits
$depositQuery = "SELECT COUNT(*) as deposit_count FROM coin_requests WHERE user_id = $userID AND status = 'approved'";
$depositResult = mysqli_query($conn, $depositQuery);
$depositData = mysqli_fetch_assoc($depositResult);
$depositCount = $depositData['deposit_count'];

// Get the number of approved withdrawals
$withdrawalQuery = "SELECT COUNT(*) as withdrawal_count FROM withdrawal_requests WHERE user_id = $userID AND status = 'Approved and Please check your Wallet'";
$withdrawalResult = mysqli_query($conn, $withdrawalQuery);
$withdrawalData = mysqli_fetch_assoc($withdrawalResult);
$withdrawalCount = $withdrawalData['withdrawal_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/profile/default_pfp.png">
    <title>Your Profile</title>

    <!-- Style link -->
    <link id="themeStylesheet" rel="stylesheet" href="./light-mode.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Dark Mode -->
    <script src="./darkMode.js"></script>

    <style>
        .show {
            display: block;
        }
        .button-container {
            display: flex;
            gap: 60%; /* Add space between buttons */
            margin-top: 20px;
        }
    </style>
</head>

<body>
<script>
        // Function to load the translation files for the selected language from both folders
    function loadTranslation(lang) {
        const historyUrl = `./translations/profile/${lang}.json`; // Path to the history folder
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
    <h1 class="heading m-auto text-4xl" data-translate="account_settings">Account Settings</h1>
    <br><br>
    <!-- main container -->
    <div class="main-container-pfp" id="main-cont">
        <!-- Info Container -->
        <div class="info-container" id="info-cont">
            <h2 class="text-2xl" data-translate="my_profile">My Profile</h2>
            <br>
            <!-- Name Div -->
            <div class="name-container">
                <div class="name-sub-container">
                    <img src="./assets/profile/<?php echo $profile ?>" alt="Profile">
                    <!-- Details -->
                    <span>
                        <h3 class="text-2xl font-bold">
                            <?php echo $name ?>
                        </h3> <!-- Name -->
                    </span>
                </div>
            </div>
            <br>
            <!-- Personal Information -->
            <div class="personal-info-container">
                <!-- Heading -->
                <div class="personal-info-heading-container">
                    <h4 class="text-2xl" data-translate="personal_information">Personal Information</h4>
                </div>
                <br>
                <!-- Details Container -->
                <div class="personal-info-detail-container">
                    <!-- Name -->
                    <div>
                        <h5 class="text-2xl font-bold" data-translate="name">Name</h5>
                        <p class="text-[18px]">
                            <?php echo $name ?>
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <h5 class="text-2xl font-bold" data-translate="email_address">Email address</h5>
                        <p class="text-[18px]">
                            <?php echo $email ?>
                        </p>
                    </div>

                    <!-- Deposit Count -->
                    <div>
                        <h5 class="text-2xl font-bold" data-translate="no_of_deposits">No. of Deposits</h5>
                        <p class="text-[18px]">
                            <?php echo $depositCount ?>
                        </p>
                    </div>

                    <!-- Withdrawal Count -->
                    <div>
                        <h5 class="text-2xl font-bold" data-translate="no_of_withdrawals">No. of Withdrawals</h5>
                        <p class="text-[18px]">
                            <?php echo $withdrawalCount ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons Section with Horizontal Layout -->
            <div class="button-container">
                <a href="./php/user_function/user_balance.php" class="py-5 px-10 bg-[#78B7D0] rounded-md text-white flex items-center gap-2" data-translate="check_balances">
                    <i class="fas fa-wallet"></i> Check Balances
                </a>
                <a href="./php/user_function/fundpw.php" class="py-5 px-10 bg-[#78B7D0] rounded-md text-white flex items-center gap-2" data-translate="create_fund_password">
                    <i class="fas fa-key"></i> Create Fund Password
                </a>
            </div>
        </div>
    </div>


        <!-- ===================================JavaScripts=============================== -->

    <!-- Side menu icon clicking -->
    <script src="./js/dropdownScript.js"></script>
</body>

</html>
