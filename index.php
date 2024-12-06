<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bithumbnn</title>

    <!-- Tag icon link -->
    <link rel="icon" href="#">

    <!-- Style link -->
    <link id="themeStylesheet" rel="stylesheet" href="./style/mainStyle.css">
    <link rel="stylesheet" href="./style/respDropdown.css"> <!-- For mobile phone dropdown button -->
    <link rel="stylesheet" href="./style/navigation.css">
    <!-- <link rel="stylesheet" href="./style/marketsStyle.css"> -->
    <!-- <link rel="stylesheet" href="./style/cryptoNews.css"> -->
    <link rel="stylesheet" href="./style/footer.css">
    <link rel="stylesheet" href="./style/helpCenter.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                <img src="./assets/images/LOGO.png" alt="LOGO" class="web-logo w-8 mx-3">
                <h1 class="text-2xl font-bold"> Bithumbnn</h1>
            </div>

            <div id="non-mobile" hidden>
                <!-- Buy Crypto -->
                <div class="dropdown">
                    <button class="dropbtn">Buy Crypto <i class="fa-solid fa-caret-down drop-down-arrow"></i></button>
                    <div class="dropdown-content">

                        <!-- Deposit -->
                        <a href="./php/user_function/depo_adding.html"><img src="./assets/images/deposit.png" alt="Deposit">
                            <span>
                                <h5>Deposit</h5>
                                <p>Crypto deposit and your records</p>
                            </span>
                        </a>

                        <!-- Withdraw -->
                        <a href="./php/user_function/adding-withdraw.html"><img src="./assets/images/withdraw.png" alt="Withdraw">
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
                        <a href="./marketData.html"><img src="./assets/images/web-analytics.png" alt="Market Data">
                            <span>
                                <h5>Market Data</h5>
                                <p>Capture market opportunities</p>
                            </span>
                        </a>

                        <!-- Feed -->
                        <a href="./cryptoNews.html"><img src="./assets/images/feed.png" alt="Feed">
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
                        <a href="./php/user_function/user_balance.php"><img src="./assets/images/tax-credit.png" alt="User Balance Profile Pic">
                            <span>
                                <h5>User Balance Profile</h5>
                                <p>Your balance and your transaction history</p>
                            </span>
                        </a>

                        <!-- Coin Exchange -->
                        <a href="./php/user_function/exchange.php"><img src="./assets/images/exchange (1).png" alt="Spot Trading">
                            <span>
                                <h5>Coin Exchange</h5>
                                <p>Easily trade with any crypto combination</p>
                            </span>
                        </a>

                        <!-- Trend Trading -->
                        <a href="./php/trade_order/btcusdt.php"><img src="./assets/images/technical-analysis.png"
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
                        <a href="./helpCenter.html"><img src="./assets/images/help.png" alt="Help Center Pic">
                            <span>
                                <h5>Help Center</h5>
                                <p>ready to help you</p>
                            </span>
                        </a>

                        <!-- Records History -->
                        <a href="./recordsHistory.html"><img src="./assets/images/clock.png" alt="History Pic">
                            <span>
                                <h5>Records History</h5>
                                <p>Footprints are here</p>
                            </span>
                        </a>

                        <!-- Customer Service -->
                        <a href="#"><img src="./assets/images/insurance-agent.png" alt="Customer Service">
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
                    <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                        <span>
                            <h5>Academy</h5>
                            <p>Master cryptocurrency knowledge</p>
                        </span>
                    </a>

                    <!-- Academy -->
                    <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                        <span>
                            <h5>Academy</h5>
                            <p>Master cryptocurrency knowledge</p>
                        </span>
                    </a>

                    <!-- Academy -->
                    <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
                        <span>
                            <h5>Academy</h5>
                            <p>Master cryptocurrency knowledge</p>
                        </span>
                    </a>

                    <!-- Academy -->
                    <a href="#"><img src="./assets/images/mortarboard.png" alt="Academy">
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
            <?php
            session_start();
            if (!isset($_SESSION["user_id"])) {
            ?>
                <a href="./login.php">Log In</a>
                <a href="./signup.html"><span class="signup-btn">Sign Up</span></a>
            <?php
            } else {
            ?>
                <a href="./profile.html"><img src="./assets/images/profile.png" alt="profile" class="w-12 inline-block"></a>

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
                    <ul class="w-[90%] m-auto text-white mobile">
                        <li>
                            <a href="#"><i class="fa-brands fa-gg-circle text-2xl mr-3"></i>P2P Trading</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa-solid fa-people-arrows text-2xl mr-3"></i>Third-party Trading</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa-solid fa-square-poll-vertical text-2xl mr-3"></i>Market
                                Overview</a>
                        </li>
                        <li>
                            <a href="./marketData.html"><i
                                    class="fa-solid fa-magnifying-glass-chart text-2xl mr-3"></i>Market Data</a>
                        </li>
                    </ul>

                    <?php
                    if (isset($_SESSION['user_id'])) {
                    ?>
                        <a href="#" class="w-[300px] bg-[#78B7D0] text-center rounded-md">Trade Now</a>
                    <?php
                    }
                    ?>

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
    <main>
        <!-- Entry Section -->
        <section class="entry-section">
            <!-- Content Section -->
            <div class="content-container">
                <h1>The Global<br>
                    Cryptocurrency <br>
                    Exchange</h1>
                <p>Making Crypto Trading Easier</p>
                <form class="email-acc-container my-10">
                    <input type="text" id="email-acc"
                        class="border px-[30px] py-[10px] rounded-[5px] w-[300px] md:w-[250px] max-sm:w-[200px]"
                        name="email-acc" placeholder="Email Account">
                    <input type="submit" class="signup-btn" name="signup-btn" value="Sign Up">
                </form>
            </div>

            <!-- Image Section -->
            <img src="./assets/images/LOGO.png" alt="Image Section">
        </section>

        <div class="align-margin"> <!--align the margin-->
            <!-- Currency Rate Section -->
            <section class="currency-rate">
                <div>
                    <div class="stock-ticker">
                        <ul>
                            <li class="minus">
                                <a href="./php/Coins/btcusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.svg" alt="BTC Logo"
                                        class="logo">
                                    <div class="pair">BTC/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/ethusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/ethereum-eth-logo.svg" alt="ETH Logo"
                                        class="logo">
                                    <div class="pair">ETH/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/ltcusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/litecoin-ltc-logo.svg" alt="LTC Logo"
                                        class="logo">
                                    <div class="pair">LTC/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/xrpusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/xrp-xrp-logo.svg" alt="XRP Logo"
                                        class="logo">
                                    <div class="pair">XRP/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/adausdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/cardano-ada-logo.svg" alt="ADA Logo"
                                        class="logo">
                                    <div class="pair">ADA/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/dotusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/polkadot-new-dot-logo.svg" alt="DOT Logo"
                                        class="logo">
                                    <div class="pair">DOT/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/bnbusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/binance-coin-bnb-logo.svg" alt="BNB Logo"
                                        class="logo">
                                    <div class="pair">BNB/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/solusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/solana-sol-logo.svg" alt="SOL Logo"
                                        class="logo">
                                    <div class="pair">SOL/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/dogeusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/dogecoin-doge-logo.svg" alt="DOGE Logo"
                                        class="logo">
                                    <div class="pair">DOGE/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/uniusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/uniswap-uni-logo.svg" alt="UNI Logo"
                                        class="logo">
                                    <div class="pair">UNI/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>

                            <li class="plus">
                                <a href="./php/Coins/avaxusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/avalanche-avax-logo.svg" alt="AVAX Logo"
                                        class="logo">
                                    <div class="pair">AVAX/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <!-- <li class="minus">
                              <a href="https://example.com/bsv" class="crypto-box">
                                  <img src="https://cryptologos.cc/logos/bitcoin-sv-bsv-logo.svg" alt="BSV Logo" class="logo">
                                  <div class="pair">BSV/USDT<span class="percentage">Loading...</span></div>
                                  <div class="price">Loading...</div>
                              </a>
                          </li>   -->
                        </ul>

                        <ul aria-hidden="true">
                            <li class="minus">
                                <a href="./php/Coins/btcusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.svg" alt="BTC Logo"
                                        class="logo">
                                    <div class="pair">BTC/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/ethusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/ethereum-eth-logo.svg" alt="ETH Logo"
                                        class="logo">
                                    <div class="pair">ETH/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/ltcusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/litecoin-ltc-logo.svg" alt="LTC Logo"
                                        class="logo">
                                    <div class="pair">LTC/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/xrpusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/xrp-xrp-logo.svg" alt="XRP Logo"
                                        class="logo">
                                    <div class="pair">XRP/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/adausdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/cardano-ada-logo.svg" alt="ADA Logo"
                                        class="logo">
                                    <div class="pair">ADA/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/dotusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/polkadot-new-dot-logo.svg" alt="DOT Logo"
                                        class="logo">
                                    <div class="pair">DOT/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/bnbusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/binance-coin-bnb-logo.svg" alt="BNB Logo"
                                        class="logo">
                                    <div class="pair">BNB/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/solusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/solana-sol-logo.svg" alt="SOL Logo"
                                        class="logo">
                                    <div class="pair">SOL/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="plus">
                                <a href="./php/Coins/dogeusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/dogecoin-doge-logo.svg" alt="DOGE Logo"
                                        class="logo">
                                    <div class="pair">DOGE/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <li class="minus">
                                <a href="./php/Coins/uniusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/uniswap-uni-logo.svg" alt="UNI Logo"
                                        class="logo">
                                    <div class="pair">UNI/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>

                            <li class="plus">
                                <a href="./php/Coins/avaxusdt.php" class="crypto-box">
                                    <img src="https://cryptologos.cc/logos/avalanche-avax-logo.svg" alt="AVAX Logo"
                                        class="logo">
                                    <div class="pair">AVAX/USDT<span class="percentage">Loading...</span></div>
                                    <div class="price">Loading...</div>
                                </a>
                            </li>
                            <!-- <li class="minus">
                              <a href="https://example.com/bsv" class="crypto-box">
                                  <img src="https://cryptologos.cc/logos/bitcoin-sv-bsv-logo.svg" alt="BSV Logo" class="logo">
                                  <div class="pair">BSV/USDT<span class="percentage">Loading...</span></div>
                                  <div class="price">Loading...</div>
                              </a>
                          </li>            -->

                        </ul>
                    </div>
                    <script src="./js/l-r-loop.js"></script> <!-- Link to the external JS file -->
                </div>
            </section>

            <!-- Rankings and Breaking Section -->
            <section class="ranking-breaking">

                <!-- Crypto Ranking -->
                <div class="crypto-ranking">
                    <div class="container">
                        <div class="header">
                            <h1 class="text-3xl md:text-2xl font-bold">Rankings</h1>
                            <button class="more-button" onclick="window.location.href='markets.html'">
                                More
                                <i class="fa-solid fa-up-right-and-down-left-from-center ml-2"></i>
                            </button>
                        </div>

                        <!-- Table menus -->
                        <!-- <div class="table-nav">
                            <a href="#">Hot</a>
                            <a href="#">New</a>
                            <a href="#">Gainers</a>
                            <a href="#">Losers</a>
                            <a href="#">Leaders</a>
                            <a href="#">Market Cap</a>
                        </div>
                        <hr> -->

                        <!-- Table-1: Hot table -->
                        <div id="hot">
                            <table id="crypto-table">
                                <thead>
                                    <tr>
                                        <th>Coins</th>
                                        <th>Price [USD]</th>
                                        <th>24H Change</th>
                                        <th>Market Cap [USD]</th>
                                    </tr>
                                </thead>

                                <!-- Hot data in table body -->
                                <tbody id="crypto-body">
                                    <!-- Data will be inserted here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script src="./js/tableScript.js"></script>
                </div>

                <!-- Crypto Breaking news -->
                <div class="crypto-news">
                    <div class="container">
                        <div class="header">
                            <h1 class="text-3xl md:text-2xl font-bold">Crypto News</h1>
                            <button class="more-button" onclick="window.location.href='markets.html'">
                                <!-- For more-btn-->
                                More
                                <i class="fa-solid fa-up-right-and-down-left-from-center ml-2"></i>
                            </button>
                        </div>
                        <div id="news-scroll-area">
                            <ul id="news-list"></ul>
                        </div>
                    </div>
                    <script src="./js/newsScript.js"></script>
                </div>
            </section>

            <!-- Android & IOS Download Section -->
            <section class="download-container">
                <!-- Image Div -->
                <div class="w-[90%] h-[90%] m-auto overflow-hidden">
                    <img src="./assets/images/download_bg.webp" alt="phone photo" class="phone-pic" id="phone-bg">
                </div>
                <!-- Content Div -->
                <div>
                    <span class="leading-[60px] text-5xl font-bold tracking-wider download-header">
                        <h1>Trade crypto. Anytime. <br>
                            Anywhere.</h1>
                    </span>
                    <ul class="list-disc my-10 list-inside">
                        <li>Thousands of assets in the palm of your hand</li>
                        <li>Powering 10, 000 transactions per second</li>
                        <li>In-depth market analysis</li>
                    </ul>

                    <!-- Download Info Container -->
                    <div class="info-container">

                        <!-- QR container -->
                        <div class="qr-container">
                            <img src="./assets/images/qr-code.png" alt="QR code" class="w-[50px]">
                            <span>
                                <p class="text-gray-500 text-[10px]">Scan to Download Bithumbnn App</p>
                                <h3 class="font-bold">iOS & Android</h3>
                            </span>
                        </div>

                        <div class="app-container">
                            <!-- Apple Store -->
                            <a href="#" class="app-shops">
                                <i class="fa-brands fa-apple"></i>
                                <h5>App Store</h5>
                            </a>

                            <!-- Google Play -->
                            <a href="#" class="app-shops">
                                <i class="fa-brands fa-google-play"></i>
                                <h5>Google Play</h5>
                            </a>

                            <!-- Android APK -->
                            <a href="" class="app-shops">
                                <i class="fa-brands fa-android"></i>
                                <h5>Android APK</h5>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- FAQs -->
        <section class="faqs" data-aos="fade-up">
            <span class="blur-spot"></span>
            <h1>FAQs</h1>

            <div class="faqs-main-container">
                <!-- first div -->
                <div class="first-faq-div">
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>Forgot the funds password processing method for platform transactions</h2>
                            <p class="faqs-text">If you forget the trading fund password on the platform,please go to
                                "My-Settings-Click SetFund Password" or contact customer service to reset
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>What is the value of the stop loss and profit setting in opening a position?
                                <br>
                                How should it be set?
                            </h2>
                            <p class="faqs-text">Take profit and stop loss as the upper limit of profit and loss set by
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
                            <h2>How to reduce contract risk?</h2>
                            <p class="faqs-text">You can transfer the available assets of the remaining accounts to the
                                contract account bytransferring funds, or reduce the risk by reducing the open
                                positions.
                            </p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>What does the quantity in the contract opening mean?</h2>
                            <p class="faqs-text">The quantity in the open position represents the number of currencies
                                you expect to buy. Forexample: select on the opening page of the BTC/USDT trading pair,
                                buy long, enter the price as1000USDT, and enter the amount as 10BTC, it means: you
                                expect to buy 10 BTC with a unit price of1000USDT.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>How is the handling fee in the contract calculated?</h2>
                            <p class="faqs-text">Handling fee-opening price*opening quantity*handling fee rate</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>Notes on forced liquidation</h2>
                            <p class="faqs-text">Risk is a measure of the risk of your assets. When the risk is equal to
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
                            <h2>What are the contract trading rules?</h2>
                            <p class="faqs-text">Transaction type

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
                            <h2>What are limit order and market order?</h2>
                            <p class="faqs-text">The limit order refers to the price you expect to entrust the platform
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
                            <h2>What does the risk level of contract transactions represent</h2>
                            <p class="faqs-text">Risk degree is a risk indicator in your contract account . A risk
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
                            <h2>Why is currency exchange necessary?</h2>
                            <p class="faqs-text">The purpose of the exchange is to allow the smooth circulation of funds
                                in different currencies inyour assets, and the QcC obtained in the futures account can
                                be freely converted into USDT fortrading. USDT in other accounts can also be freely
                                converted to QcC for trading.</p>
                        </span>
                        <i class="fa-solid fa-plus" onclick="plus_minus(this);showorhide(this)"></i>
                    </div>
                    <hr>
                    <div class="faqs-container">
                        <span class="faqs-content">
                            <h2>How to buy USDT through the platform?</h2>
                            <p class="faqs-text">Method 1: Select the order you want to buy through the platform buy and
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
                            <h2>Why does the converted amount of assets change?</h2>
                            <p class="faqs-text">The equivalent calculation in the asset is the value of the current
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
                        <h1 class="text-2xl font-bold">Bithumbnn</h1>
                    </div>
                    <p>Making Crypto Trading Easier</p>
                </span>

                <!-- Icon Container -->
                <span class="icon-container">
                    <!-- Telegram Account -->
                    <a href="#"><img src="./assets/images/telegram.png" alt="Telegram" class="contact-icons"></a>
                    <!-- Email -->
                    <a href="#"><img src="./assets/images/gmail.png" alt="Email" class="contact-icons"></a>
                    <p>© 2024-2025 Bithumbnn.com. All rights reserved.</p>
                </span>
            </div>

            <!-- About -->
            <ul>
                <span class="gen-info-container">
                    <h2 class="text-2xl font-bold mb-5" onclick="myFunction_gsm('sec1')">About</h2><i
                        class="fa-solid fa-angle-right" id="right-arrow1"></i>
                </span>
                <span id="sec1">
                    <li><a href="./helpCenter.html">About us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="./termAgreement.html">Terms and policy</a></li>
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
                    <li>Future Trading</li>
                    <li>Global Market</li>
                    <li>Top Gainers</li>
                    <li>Top Losers</li>
                    <li>Coin Exchange</li>
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
                    <li><a href="./helpCenter.html">Help Center</a></li>
                    <li>Secure Secirity</li>
                    <li>Balance</li>
                    <li>Official Version</li>
                </span>
            </ul>
        </div>
        <!-- Footer baseline (Second Container)-->
        <div class="footer-baseline">
            <span class="align-margin flex justify-between leading-[50px] text-white">
                <h3>Version: 1.0</h3>
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

    <!-- FAQs Scrip -->
    <script>
        // FAQs Plus / Minus
        $(document).ready(function() {
            $(this).click(function() {
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
                x.style.color = "#0095ff"; // Change icon color to blue
                faqTitle.style.color = "#0095ff"; // Change h2 color to blue
            } else {
                x.classList.add("fa-plus");
                x.classList.remove("fa-minus");
                x.style.color = "#000000"; // Change icon color back to black
                faqTitle.style.color = "#000000"; // Change h2 color back to black
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

    <?php
    session_destroy();
    ?>

</body>

</html>