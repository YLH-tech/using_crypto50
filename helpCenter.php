<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/images/help.png">
    <title>Help</title>

    <!-- Style Links -->
    <!-- <link rel="stylesheet" href="./style/helpCenter.css">
    <link rel="stylesheet" href="./style/footer.css">
    <link rel="stylesheet" href="./style/navigation.css">
    <link rel="stylesheet" href="./style/dropDownBtn.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="./style/light-mode1.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- night mode script -->
    <script src="./js/darkMode1.js"></script>
</head>

<body>
    <!-- This script is for translation  -->
    <script>
        // Function to load the translation file for the selected language
        function loadTranslation(lang) {
            const url = `./translations/${lang}.json`; // Assuming translations are in the 'translations' folder
            return fetch(url)
                .then(response => response.json())
                .then(translations => {
                    applyTranslations(translations);
                    return translations; // Return translations for further use
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

    <main>
        <!-- Intro Page -->
        <div class="intro-container">
            <!-- Intro content -->
            <div class="intro-content">
                <h1 class="xl:text-6xl md:text-3xl sm:text-5xl font-bold" data-translate="h1">Ready to help you!</h1>
                <br>
                <p class="xl:text-4xl md:text-2xl sm:text-4xl" data-translate="p">Bithumbnn supports all cryptocurrency traders in achieving financial success</p>
                <br>
                <a href="tel:" data-translate="call_now"><button data-translate="call_button">Call Now</button></a>
            </div>
            <img src="./assets/images/Active Support-bro.svg" alt="Support Pic" data-translate="support_pic">
        </div>

        <!-- Contact Us -->
    <div class="contact-div">
        <!-- First Container -->
        <div class="contact-sub-container">
            <h1 data-translate="footer_about_us">About us</h1>
            <p data-translate="about_us_p">Bithumbnn empowers individuals and businesses to navigate the exciting world of cryptocurrency with
                 confidence. Our mission centers on providing secure, user-friendly, and innovative solutions
                  tailored to your trading needs. With a commitment to excellence and trust, we aim to redefine how
                   people connect with the digital economy.</p>
        </div>
        <br class="md:block" hidden>
        <!-- Tablet & Mobile mode -->
        <div class="md:flex md:justify-between tablet-mobile-mode">
            <img src="./assets/images/Contact us-amico.svg" alt="Contact Us Pic" class="w-[400px] md:m-auto md:w-[300px] contact-img" data-translate="contact_pic">
            <!-- Second Container -->
            <div class="contact-sub-container contact-menu-container">
                <!-- Gmail -->
                <a href="mailto:" class="contact-menus"><img src="./assets/images/gmail.png" alt="Gamil Icon">example@gmail.com</a>
                <br>
                <!-- Telegram -->
                <a href="#" class="contact-menus"><img src="./assets/images/telegram.png" alt="Telegram Icon">@someoneusername</a>
            </div>
        </div>
    </div>


        <!-- FAQs -->
        <section class="faqs" data-aos="fade-up">
            <span class="blur-spot"></span>
            <h1 data-translate="FAQs">FAQs</h1>

            <div class="faqs-main-container">
                <!-- first div -->
                <div class="first-faq-div">
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq00">Forgot the funds password processing method for platform transactions</h2>
                            <p class="faqs-text" data-translate="faq01">If you forget the trading fund password on the platform,please go to
                                "My-Settings-Click SetFund Password" or contact customer service to reset
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq10">What is the value of the stop loss and profit setting in opening a position?
                                <br>
                                How should it be set?
                            </h2>
                            <p class="faqs-text" data-translate="faq11">Take profit and stop loss as the upper limit of profit and loss set by
                                you. When the set amount ofstop profit and stop loss is reached, the system will
                                automatically close the position. It can be usedfor risk control when you buy a
                                contract. Half of the set profit-taking amount is: amount of increasex quantity x
                                leverage multiple, set stop loss.
                                <br>
                                We recommend that you set it according to your actual asset situation and reasonably
                                controlthe risk.
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq20">How to reduce contract risk?</h2>
                            <p class="faqs-text" data-translate="faq21">You can transfer the available assets of the remaining accounts to the
                                contract account bytransferring funds, or reduce the risk by reducing the open
                                positions.
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq30">What does the quantity in the contract opening mean?</h2>
                            <p class="faqs-text" data-translate="faq31">The quantity in the open position represents the number of currencies
                                you expect to buy. Forexample: select on the opening page of the BTC/USDT trading pair,
                                buy long, enter the price as1000USDT, and enter the amount as 10BTC, it means: you
                                expect to buy 10 BTC with a unit price of1000USDT.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq40">How is the handling fee in the contract calculated?</h2>
                            <p class="faqs-text" data-translate="faq41">Handling fee-opening price*opening quantity*handling fee rate</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq50">Notes on forced liquidation</h2>
                            <p class="faqs-text" data-translate="faq51">Risk is a measure of the risk of your assets. When the risk is equal to
                                100%, your position isregarded as a liquidation, and the risk = (position
                                margin/contract account equity)

                                *100%, in order to prevent users from penetrating the position, the system sets the risk
                                adjustmentratio. When the risk reaches the risk value set by the system, the system will
                                force the position tobe closed. For example: the adjustment ratio set by the system is
                                10%, when your risk is greaterthan or equal to 90%, all your positions will be forced to
                                liquidate by the system

                                Note: If the system's forced liquidation is caused by excessive risk, all your positions
                                will beliquidated. Therefore, I hope you can reasonably control your risk to avoid
                                unnecessary losses.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                </div>

                <!-- second div -->
                <div class="second-faq-div">
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq60">What are the contract trading rules?</h2>
                            <p class="faqs-text" data-translate="faq61">Transaction type

                                Trading types are divided into two directions: long positions(buy) and short positions
                                (sell):

                                Buy long (bullish) means that you think that the current index is likely to rise, and
                                you want to buya certain number of certain contracts at the price you set or the current
                                market price.

                                Sell short (bearish) means that you think that the current index is likely to fall, and
                                you want to sellla certain number of new contracts at a price you set or the current
                                market price.Ordering methodLimited price order: you need to specify the price and
                                quantity of the order placed

                                Market order: you only need to set the order quantity, the price is the current market
                                pricePositions

                                When the order you submit for opening a position is completed, it is called a position

                                Contract delivery issues

                                The platform contract is a perpetual contract with no set delivery time. As long as the
                                system doesnot meet the conditions for liquidation or you do not manually close the
                                position, you can hold theposition permanently.
                                <br>
                                System liquidation
                                <br>
                                1: The system will automatically close the position if the set value of Take Profit and
                                Stop Loss isreached
                                <br>
                                2: The risk is too high, the system is forced to close the position
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq70">What are limit order and market order?</h2>
                            <p class="faqs-text" data-translate="faq71">The limit order refers to the price you expect to entrust the platform
                                to trade, and the marketorder refers to the direct entrustment of the platform to trade
                                at the current price. In the rules foropening a position, market orders take precedence
                                over limit orders.
                                <br>
                                If you choose a limit order, please open a position reasonably based on the current
                                currencymarket price to avoid losses due to unreasonable opening prices
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>

                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq80">What does the risk level of contract transactions represent</h2>
                            <p class="faqs-text" data-translate="faq81">Risk degree is a risk indicator in your contract account . A risk
                                degree equal to 100% isconsidered as a liquidation. We suggest that when your risk
                                exceeds 50%, you need to open yourposition carefully to avoid losses due to liquidation.
                                You can reduce your risk by replenishing theavailable funds of contract assets, or
                                reducing your positions.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq90">Why is currency exchange necessary?</h2>
                            <p class="faqs-text" data-translate="faq91">The purpose of the exchange is to allow the smooth circulation of funds
                                in different currencies inyour assets, and the QcC obtained in the futures account can
                                be freely converted into USDT fortrading. USDT in other accounts can also be freely
                                converted to QcC for trading.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq100">How to buy USDT through the platform?</h2>
                            <p class="faqs-text" data-translate="faq101">Method 1: Select the order you want to buy through the platform buy and
                                sell list to buy and sell.
                                <br>
                                Method 2: Click the publish button to publish a buy or sell order for quick transactions
                                according to your needs
                                <br>
                                The system will quickly match you with buying and selling users.Note: If the purchase
                                order is notpaid after 15 minutes, the system will automatically cancel the order,
                                please pay in time. If the orderis cancelled more than 3 times on the same day, the
                                transaction cannot be performed again on thesame day, and the trading authority will be
                                restored the next day
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2 data-translate="faq110">Why does the converted amount of assets change?</h2>
                            <p class="faqs-text" data-translate="faq111">The equivalent calculation in the asset is the value of the current
                                holding digital currency
                                converted into USDT,
                                which changes due to the price fluctuation of the digital asset.
                                The number of your digital assets has not changed.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                </div>
            </div>
        </section>
    </main>

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

    <!-- JavaScripts -->

    <!-- Clicking the clickdown btn -->
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
    <!-- FAQs Scrip -->
    <script>
        // FAQs Plus / Minus
        $(document).ready(function () {
            $(this).click(function () {
                $(this).toggleClass("hide");
            });
        });

        function plus_minus(x) {
            // Find the h2 tag within the same container
            const faqContainer = x.closest('.faqs-container');
            const faqTitle = faqContainer.querySelector('h2');

            if (x.classList.contains("fa-plus")) {
                x.classList.add("fa-minus");
                x.classList.remove("fa-plus");
                // x.style.color = "#0095ff"; // Change icon color to blue
                // faqTitle.style.color = "#0095ff"; // Change h2 color to blue
            } else {
                x.classList.add("fa-plus");
                x.classList.remove("fa-minus");
                // x.style.color = "#000000"; // Change icon color back to black
                // faqTitle.style.color = "#000000"; // Change h2 color back to black
            }
        }

        function showorhide(element) {
            // Find the <p> tag within the same container
            const faqContainer = element.closest('.faqs-container');
            const faqText = faqContainer.querySelector('.faqs-text');

            // Toggle the display of the <p> tag with a smooth transition
            if (faqText.classList.contains('show')) {
                // Trigger the pop-down animation
                faqText.style.animation = 'popDown 0.5s forwards';
                setTimeout(() => {
                    faqText.classList.remove('show'); // Remove the class after animation ends
                    faqText.style.display = 'none'; // Ensure it is hidden after animation
                    element.classList.remove('fa-minus'); // Change back to plus icon
                    element.classList.add('fa-plus');
                    element.style.color = 'black'; // Reset icon color to black after hiding (optional)
                }, 500); // Match the duration of the animation
            } else {
                faqText.style.display = 'block'; // Show the element first
                faqText.classList.add('show'); // Then add the class to trigger the animation
                faqText.style.animation = 'popUp 0.5s forwards';
                element.classList.remove('fa-plus'); // Change to minus icon
                element.classList.add('fa-minus');
                element.style.color = ''; // Reset icon color (optional, based on your styling)
            }
        }
    </script>

    <!-- Animation -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 2000,
            once: true,
        });
    </script>
</body>

</html>