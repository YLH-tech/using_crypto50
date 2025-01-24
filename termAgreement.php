<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="terms-heading">Terms and Agreements</title>

    <!-- Style links -->
    <!-- <link rel="stylesheet" href="./termAgreement.css"> -->
    <link id="themeStylesheet" rel="stylesheet" href="./light-mode.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="./darkMode.js"></script>
    <script src="./js/external_translate.js"></script>

</head>

<body>
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
    <main class="main-container-term">
        <h1 id="heading"><img src="./assets/images/bithumbnn_LOGO.png" alt="LOGO" id="logo-pic">Bithumbnn</h1>
        <p id="description" data-translate="description_tem">Future Trading</p>

        <br>
        <br><br>
        <!-- Terms and Agreements Container -->
        <div class="term-agreement-container">
            <h1 class="main-heading" data-translate="terms-heading">Terms and Conditions</h1>
            <br><br>
            <div class="term-agreement-sub-container">
                <h1 id="welcome-heading" data-translate="welcome-heading">Welcome to Bithumbnn, the Future of Cryptocurrency Trading!</h1>

                <h2 data-translate="acceptance-heading"> Acceptance of Terms</h2>
                <p data-translate="acceptance-text">By accessing Bithumbnn and using the services provided herein, you acknowledge that you have read,
                    understood,
                    and agreed to be bound by these Terms and Conditions. If you do not accept these terms, you must not
                    register or
                    continue to use our services.</p>

                <h2 data-translate="eligibility-heading">Eligibility to Participate</h2>
                <p data-translate="eligibility-text">You affirm that you are of legal age and have the legal capacity to enter into this agreement.
                    Bithumbnn
                    is not
                    available to persons under the age of 18 or users previously suspended or removed from the platform.
                </p>

                <h2 data-translate="account-integrity-heading">Account Integrity</h2>
                <p data-translate="account-integrity-text">Upon creating an account with Bithumbnn, you agree to provide truthful and accurate information and
                    to
                    maintain
                    the accuracy of such information. The security of your account credentials is your responsibility,
                    and
                    any
                    activities under said account will be deemed as having been performed by you.</p>

                <h2 data-translate="services-offered-heading">Services Offered</h2>
                <p data-translate="services-offered-text">Bithumbnn provides a cutting-edge digital platform for trading a variety of cryptocurrencies. We
                    reserve
                    the
                    right to modify, suspend, or discontinue any aspect of our services at any time, without notice.</p>

                <h2 data-translate="financial-terms-heading">Financial Terms</h2>
                <p data-translate="financial-terms-text">Fees for transactions and other services are subject to change and may vary based on market
                    conditions.
                    All
                    financial transactions on Bithumbnn must comply with the financial regulations and standards set
                    forth
                    by
                    relevant authorities.</p>

                <h2 data-translate="risk-heading">Risk Acknowledgment</h2>
                <p data-translate="risk-text">Trading cryptocurrencies involves significant risk. Prices can fluctuate on any given day. Due to
                    such
                    price
                    fluctuations, you may increase or lose value in your assets at any given moment. You are solely
                    responsible for
                    your trading decisions and the risks associated with cryptocurrency trading.</p>

                <h2 data-translate="prohibited-conduct-heading">Prohibited Conduct</h2>
                <p data-translate="prohibited-conduct-text">You are prohibited from engaging in any form of illegal activity on Bithumbnn, including but not
                    limited
                    to
                    fraud, money laundering, and other activities that could involve unlawful behavior.</p>

                <h2 data-translate="intellectual-property-heading">Intellectual Property Rights</h2>
                <p data-translate="intellectual-property-text">All content on Bithumbnn, including text, graphics, logos, and software, is the exclusive property of
                    Bithumbnn
                    and is protected by intellectual property laws. You may not use any content from our platform
                    without
                    express
                    permission.</p>

                <h2 data-translate="privacy-assurance-heading">Privacy Assurance</h2>
                <p data-translate="privacy-assurance-text">Your privacy is important to us. Please review our Privacy Policy, which explains how we handle
                    personal
                    information. The Privacy Policy is part of these Terms and is incorporated by reference to provide
                    you
                    with full
                    disclosure.</p>

                <h2 data-translate="account-termination-heading">Account Termination</h2>
                <p data-translate="account-termination-text">You may terminate your Bithumbnn account at any time. We also reserve the right to suspend or
                    terminate
                    your
                    account if you violate any of the terms outlined here or engage in any activity deemed harmful to
                    Bithumbnn or
                    its users.</p>

                <h2 data-translate="dispute-resolution-heading">Dispute Resolution</h2>
                <p data-translate="dispute-resolution-text">In the event of a dispute, efforts should be made to resolve the issue amicably before resorting to
                    formal
                    arbitration or litigation.</p>

                <h2 data-translate="changes-to-terms-heading">Changes to the Terms</h2>
                <p data-translate="changes-to-terms-text">Bithumbnn reserves the right to modify these terms at any time. Your continued use of the platform
                    after
                    such
                    modifications will constitute acknowledgment and acceptance of the modified terms.</p>

                <h2 data-translate="miscellaneous-heading">Miscellaneous</h2>
                <p data-translate="miscellaneous-text">These Terms are governed by the laws of [Jurisdiction]. Should any part of these Terms be held
                    invalid or
                    unenforceable, that portion shall be construed in a manner consistent with applicable law to
                    reflect, as
                    nearly
                    as possible, the original intentions of the parties, and the remaining portions shall remain in full
                    force and
                    effect.</p>

                <h2 data-translate="contact-heading">Contact Us</h2>
                <p data-translate="contact-text">For any questions or concerns about these Terms and Conditions, please contact us at [Contact
                    Information].</p>
            </div>
            <br><br>
            <a href="./index.php" id="ok-btn" data-translate="ok-button">OK</a>
        </div>
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