<?php 
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/images/market.png">
    <title>Bithumbnn</title>

    <!-- Style link -->
    <!-- <link rel="stylesheet" href="./style/marketsStyle.css"> -->
    <link id="themeStyleSheet" rel="stylesheet" href="./style/light-mode2.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Market Data Bar Graph script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Dark Mode Script -->
    <script src="./js/darkMode2.js"></script>

</head>

<body>
   <!-- night mode script -->
   <script src="./js/darkMode.js"></script>
<!-- This script is for translation  -->
<script>
        // Function to load the translation files for the selected language from both folders
        function loadTranslation(lang) {
            const historyUrl = `./translations/marketData/${lang}.json`; // Path to the history folder
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
            <div class="flex items-center">
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
            </div>

            <div id="non-mobile" hidden>
                <!-- Buy Crypto -->
                <div class="dropdown">
                    <button class="dropbtn" data-translate="nav_buy_crypto">Buy Crypto <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                    <div class="dropdown-content">

                        <!-- Deposit -->
                        <a href="./php/user_function/depo_adding.html"><img src="./assets/images/deposit.png"
                                alt="Deposit">
                            <span>
                                <h5 data-translate="nav_deposit">Deposit</h5>
                                <p data-translate="nav_deposit_desc">Crypto deposit and your records</p>
                            </span>
                        </a>

                        <!-- Withdraw -->
                        <a href="./php/user_function/adding-withdraw.html"><img src="./assets/images/withdraw.png"
                                alt="Withdraw">
                            <span>
                                <h5 data-translate="nav_withdraw">Withdraw</h5>
                                <p data-translate="nav_withdraw_desc">Crypto withdraw and your records</p>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Markets -->
                <div class="dropdown">
                    <button class="dropbtn" data-translate="nav_markets">Markets <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
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
                    <button class="dropbtn" data-translate="nav_exchange">Exchange <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                    <div class="dropdown-content">
                        <!-- User Balance Profile -->
                        <a href="./php/user_function/user_balance.php"><img src="./assets/images/tax-credit.png"
                                alt="User Balance Profile Pic">
                            <span>
                                <h5 data-translate="nav_user_profile">User Balance Profile</h5>
                                <p data-translate="nav_user_profile_desc">Your balance and your transaction history</p>
                            </span>
                        </a>

                        <!-- Coin Exchange -->
                        <a href="./php/user_function/exchange.php"><img src="./assets/images/exchange (1).png"
                                alt="Spot Trading">
                            <span>
                                <h5 data-translate="nav_coin_exchange">Coin Exchange</h5>
                                <p data-translate="nav_coin_exchange_desc">Easily trade with any crypto combination</p>
                            </span>
                        </a>

                        <!-- Trend Trading -->
                        <a href="./php/trade_order/btcusdt.php"><img src="./assets/images/technical-analysis.png"
                                alt="Margin Trading">
                            <span>
                                <h5 data-translate="nav_trend_trading">Trend Trading</h5>
                                <p data-translate="nav_trend_trading_desc">The most popular trading nowaday</p>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- More -->
                <div class="dropdown">
                    <button class="dropbtn" data-translate="nav_more">More <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                    <div class="dropdown-content">

                        <!-- Help Center -->
                        <a href="./helpCenter.php"><img src="./assets/images/help.png" alt="Help Center Pic">
                            <span>
                                <h5 data-translate="nav_help_center">Help Center</h5>
                                <p data-translate="nav_help_center_desc">ready to help you</p>
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
        <h1 class="text-3xl m-3" data-translate="recommendedRanks">Recommended Ranks</h1>
        <div class="market-data-container">
            <!-- First Group  -->
            <div class="first-group">
                <!-- Table 2: Top Gainers -->
                <div class="table-container">
                    <!-- heading -->
                    <div class="content-container">
                        <h2 data-translate="footer_top_gainers">Top Gainers</h2>
                        <a href="#" class="more-icon"><span data-translate="More">More </span><i class="fa-solid fa-angle-right"></i></a>
                    </div>
                    <hr class="my-3">
                    <!-- Data table -->
                    <table id="gainers-table" class="tables">
                        <thead>
                            <tr>
                                <th class="text-left crypto-name" data-translate="Coins">Coin</th>
                                <th class="text-right" data-translate="Price [USD]">Price (USD)</th>
                                <th class="text-right" data-translate="24H Change">24H Change</th>
                                <th class="text-right" data-translate="Market Cap">Market Cap</th>
                            </tr>
                        </thead>
                        <tbody id="gainers-table-body" class="table-body">
                            <!-- Data will appear linking with marketsScript.js -->
                        </tbody>
                    </table>
                </div>

                <!-- Table 3: Top Losers -->
                <div class="table-container">
                    <!-- heading -->
                    <div class="content-container">
                        <h2 data-translate="footer_top_losers">Top Losers</h2>
                        <a href="#" class="more-icon"><span data-translate="More">More</span><i class="fa-solid fa-angle-right"></i></a>
                    </div>
                    <hr class="my-3">
                    <!-- Data table -->
                    <table id="losers-table" class="tables">
                        <thead>
                            <tr>
                                <th class="text-left crypto-name" data-translate="Coins">Coin</th>
                                <th class="text-right" data-translate="Price [USD]">Price (USD)</th>
                                <th class="text-right" data-translate="24H Change">24H Change</th>
                                <th class="text-right" data-translate="Market Cap">Market Cap</th>
                            </tr>
                        </thead>
                        <tbody id="loser-table-body" class="table-body">
                            <!-- Data will appear linking with marketsScript.js -->
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <!-- Second Group -->
            <div class="second-group">
                <!-- Table 1: Market Cap -->
                <div class="table-container marketcap">
                    <!-- heading -->
                    <div class="content-container">
                        <h2 data-translate="Market Cap">Market Cap</h2>
                        <a href="#" class="more-icon"><span data-translate="More">More</span><i class="fa-solid fa-angle-right"></i></a>
                    </div>
                    <hr class="my-3">
                    <!-- Data table -->
                    <table id="marketcap-table" class="tables">
                        <thead>
                            <tr>
                                <th class="text-left crypto-name" data-translate="Coins">Coin</th>
                                <th class="text-right" data-translate="Price [USD]">Price (USD)</th>
                                <th class="text-right" data-translate="24H Change">24H Change</th>
                                <th class="text-right" data-translate="Market Cap">Market Cap</th>
                            </tr>
                        </thead>
                        <tbody id="marketcap-table-body" class="table-body marketcap-body">
                            <!-- Data will appear linking with marketsScript.js -->
                        </tbody>
                    </table>
                </div>

                <!-- Market Bar Graph -->
                <div id="loading-indicator" style="display: none; text-align: center; margin: 20px;">
                    Loading...
                </div>

                <div class="container-price-distribution">
                    <h2 data-translate="priceChangeDistribution">24H Price Change Distribution</h2>
                    <div class="distribution-container">
                        <div class="distribution">
                            <div class="column-wrapper">
                                <span class="number" id="down10-count">0</span>
                                <div class="column light-red" id="down10"></div>
                                <span class="label">>10%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="down7-count">0</span>
                                <div class="column light-red" id="down7"></div>
                                <span class="label">7-10%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="down5-count">0</span>
                                <div class="column light-red" id="down5"></div>
                                <span class="label">5-7%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="down3-count">0</span>
                                <div class="column light-red" id="down3"></div>
                                <span class="label">3-5%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="down0-count">0</span>
                                <div class="column light-red" id="down0"></div>
                                <span class="label">0-3%</span>
                            </div>

                            <!-- <div class="column-wrapper">
                    <span class="number" id="zero-count">0</span>
                    <div class="column light-grey" id="zero"></div>
                    <span class="label">0%</span>
                </div> -->

                            <div class="column-wrapper">
                                <span class="number" id="up0-count">0</span>
                                <div class="column light-green" id="up0"></div>
                                <span class="label">0-3%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="up3-count">0</span>
                                <div class="column light-green" id="up3"></div>
                                <span class="label">3-5%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="up5-count">0</span>
                                <div class="column light-green" id="up5"></div>
                                <span class="label">5-7%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="up7-count">0</span>
                                <div class="column light-green" id="up7"></div>
                                <span class="label">7-10%</span>
                            </div>
                            <div class="column-wrapper">
                                <span class="number" id="up10-count">0</span>
                                <div class="column light-green" id="up10"></div>
                                <span class="label">>10%</span>
                            </div>
                        </div>
                    </div>
                    <div class="price-distribution">
                        <div class="price-down" id="total-price-down" data-translate="priceDown">Price Down: 0</div>
                        <div class="price-up" id="total-price-up" data-translate="priceUp">Price Up: 0</div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- Market Line Graph -->
        <div id="chartContainer">
            <canvas id="cryptoChart" width="600" height="250"></canvas>
            <div class="description">
                <p data-translate="clickCoin">Click on a coin's name in the chart to show or hide its prices.</p>
            </div>
        </div>
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

    <!-- Market data script -->
    <script src="./js/marketsScript.js"></script>

    <!-- Market Bar Graph Script -->
    <script src="./js/marketBarGraph.js"></script>

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