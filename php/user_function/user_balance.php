<?php
session_start();
include '../database/db_connection.php';
include 'get_real_time_prices.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['clear_record'])) {
    $clear_stmt = $conn->prepare("DELETE FROM transactions_exchange WHERE user_id = ?");
    $clear_stmt->execute([$userId]); // Use $userId instead of $user_id
    header("Location: dashboard.php"); // Refresh the page
    exit();
}


// Fetch user's current balances for displaying
$sql = "SELECT USDT, BTC, ETH,USDC, BNB, XRP, DOGE, SOL, ADA, TRX, DOT, LTC, BCH, ETC, UNI, LINK, AVAX, NEO, EOS, ARB, APT, TON FROM user_balances WHERE user_id = $userId";
$balanceResult = $conn->query($sql);
$balance = $balanceResult->fetch_assoc();




// // Direct USD estimation
$usdtBalance = $balance['USDT'] ?? 0;
$btcBalance = $balance['BTC'] ?? 0;
$ethBalance = $balance['ETH'] ?? 0;
$usdcBalance = $balance['USDC'] ?? 0;
$bnbBalance = $balance['BNB'] ?? 0;
$xrpBalance = $balance['XRP'] ?? 0;
$dogeBalance = $balance['DOGE'] ?? 0;
$solBalance = $balance['SOL'] ?? 0;
$adaBalance = $balance['ADA'] ?? 0;
$trxBalance = $balance['TRX'] ?? 0;
$dotBalance = $balance['DOT'] ?? 0;
$ltcBalance = $balance['LTC'] ?? 0;
$bchBalance = $balance['BCH'] ?? 0;
$etcBalance = $balance['ETC'] ?? 0;
$uniBalance = $balance['UNI'] ?? 0;
$linkBalance = $balance['LINK'] ?? 0;
$avaxBalance = $balance['AVAX'] ?? 0;
$neoBalance = $balance['NEO'] ?? 0;
$eosBalance = $balance['EOS'] ?? 0;
$arbBalance = $balance['ARB'] ?? 0;
$aptBalance = $balance['APT'] ?? 0;
$tonBalance = $balance['TON'] ?? 0; // TON balance

$usdtUsd = $usdtBalance * $usdtPrice;
$btcUsd = $btcBalance * $btcPrice;
$ethUsd = $ethBalance * $ethPrice;
$usdcUsd = $usdcBalance * $usdcPrice;
$bnbUsd = $bnbBalance * $bnbPrice;
$xrpUsd = $xrpBalance * $xrpPrice;
$dogeUsd = $dogeBalance * $dogePrice;
$solUsd = $solBalance * $solPrice;
$adaUsd = $adaBalance * $adaPrice;
$trxUsd = $trxBalance * $trxPrice;
$dotUsd = $dotBalance * $dotPrice;
$ltcUsd = $ltcBalance * $ltcPrice;
$bchUsd = $bchBalance * $bchPrice;
$etcUsd = $etcBalance * $etcPrice;
$uniUsd = $uniBalance * $uniPrice;
$linkUsd = $linkBalance * $linkPrice;
$avaxUsd = $avaxBalance * $avaxPrice;
$neoUsd = $neoBalance * $neoPrice;
$eosUsd = $eosBalance * $eosPrice;
$arbUsd = $arbBalance * $arbPrice;
$aptUsd = $aptBalance * $aptPrice;
$tonUsd = $tonBalance * $tonPrice;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <!-- Style Links -->
    <link rel="stylesheet" href="../../style/userDashboard.css">
    <link rel="stylesheet" href="../../style/footer.css">

    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
    <!-- night mode script -->
    <script src="../../js/darkMode.js"></script>

    <!-- Main Container -->
    <main class="main-container">
        <!-- Balance Info Div -->
        <div class="balance-info-container">
            <!-- Amount Div -->
            <div>
                <h1 class="text-5xl font-bold">Custody Dashboard</h1>
                <h2 class="text-4xl font-bold my-5">$1,000,000.00</h2>
                <h4 class="text-[14px]">$123,45 (6.78%) <i class="fa-solid fa-arrow-trend-up"></i></h4>
            </div>
            <a href="../../recordsHistory.html"><button class="py-2 px-8 bg-gray-300 rounded-3xl">View History</button></a>
        </div>
        <br><br>
        <!-- Balances Details Container -->
        <div class="balance-details-container">
            <h1 class="text-2xl font-bold">Balances</h1>
            <br>
            <h2 class="text-1xl py-1 px-10 rounded-2xl bg-black w-fit text-gray-100">Crypto Balances</h2>
            <br>
            <!-- Balance Table -->
            <table class="balance-table">
                <thead>
                    <tr class="text-right">
                        <th class="text-left">Asset</th>
                        <th>Balance</th>
                        <th>Estimate Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Coin Datas -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/BTC.png"
                                alt="Coin Icon" class="w-10"> Bitcoin</td>
                        <td> <span id="BTC_balance"><?php echo $btcBalance; ?> BTC</td>
                        <td>$<?php echo number_format($btcUsd, 2); ?> USD</span></td>
                        <td class="w-[25%]">
                            <a href="./depo_btc.php"><button class="py-2 px-10 mx-1 bg-black text-gray-100 rounded-3xl">Deposit</button></a>
                            <a href="./withdraw_btc.php"><button class="py-2 px-10 mx-1 rounded-3xl bg-gray-200">Withdraw</button></a>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/ETH.png"
                                alt="Coin Icon" class="w-10"> Ethereum</td>
                        <td><span id="ETH_balance"><?php echo $ethBalance; ?> ETH</td>
                        <td>$<?php echo number_format($ethUsd, 2); ?> USD</td>
                        <td class="w-[25%]">
                            <a href="./depo_eth.php"><button class="py-2 px-10 mx-1 bg-black text-gray-100 rounded-3xl">Deposit</button></a>
                            <a href="./withdraw_eth.php"><button class="py-2 px-10 mx-1 rounded-3xl bg-gray-200">Withdraw</button></a>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/USDT.png"
                                alt="Coin Icon" class="w-10"> Tether</td>
                        <td><span id="USDT_balance"> <?php echo $usdtBalance; ?> USDT</td>
                        <td>$<?php echo number_format($usdtUsd, 2); ?> USD</td>
                        <td class="w-[25%]">
                            <a href="./depo_usdt.php"><button class="py-2 px-10 mx-1 bg-black text-gray-100 rounded-3xl">Deposit</button></a>
                            <a href="./withdraw_usdt.php"><button class="py-2 px-10 mx-1 rounded-3xl bg-gray-200">Withdraw</button></a>
                        </td>
                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/USDC.png"
                                alt="Coin Icon" class="w-10"> USD</td>
                        <td><span id="USDC_balance"><?php echo $usdcBalance; ?> USDC</td>
                        <td>$<?php echo number_format($usdcUsd, 2); ?> USD</td>
                        <td class="w-[25%]">
                            <a href="./depo_usdc.php"><button class="py-2 px-10 mx-1 bg-black text-gray-100 rounded-3xl">Deposit</button></a>
                            <a href="./withdraw_usdc.php"><button class="py-2 px-10 mx-1 rounded-3xl bg-gray-200">Withdraw</button></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <h2 class="text-1xl py-1 px-10 rounded-2xl bg-black w-fit text-gray-100">Other Coin Balances</h2>
            <table class="balance-table">
                <thead>
                    <tr class="text-right">
                        <th class="text-left">Asset</th>
                        <th>Balance</th>
                        <th>Estimate Prices</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Coin Datas -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/BNB.png"
                                alt="Coin Icon" class="w-10"> Binance</td>
                        <td> <span id="BNB_balance"><?php echo $bnbBalance; ?> BNB</td>
                        <td>$<?php echo number_format($bnbUsd, 2); ?> USD</span></td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/XRP.png"
                                alt="Coin Icon" class="w-10"> Ripple</td>
                        <td><span id="XRP_balance"><?php echo $xrpBalance; ?> XRP</td>
                        <td>$<?php echo number_format($xrpUsd, 2); ?> USD</td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/DOGE.png"
                                alt="Coin Icon" class="w-10"> Dogecoin</td>
                        <td><span id="DOGE_balance"> <?php echo $dogeBalance; ?> DOGE</td>
                        <td>$<?php echo number_format($dogeUsd, 2); ?> USD</td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/SOL.png"
                                alt="Coin Icon" class="w-10"> Solana</td>
                        <td><span id="SOL_balance"><?php echo $solBalance; ?> SOL</td>
                        <td>$<?php echo number_format($solUsd, 2); ?> USD</td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/ADA.png"
                                alt="Coin Icon" class="w-10"> Cardano</td>
                        <td> <span id="ADA_balance"><?php echo $adaBalance; ?> ADA</td>
                        <td>$<?php echo number_format($adaUsd, 2); ?> USD</span></td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/TRX.png"
                                alt="Coin Icon" class="w-10"> Tron</td>
                        <td><span id="TRX_balance"><?php echo $trxBalance; ?> TRX</td>
                        <td>$<?php echo number_format($trxUsd, 2); ?> USD</td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/DOT.png"
                                alt="Coin Icon" class="w-10"> Polkadot</td>
                        <td><span id="DOT_balance"> <?php echo $dotBalance; ?> DOT</td>
                        <td>$<?php echo number_format($dotUsd, 2); ?> USD</td>

                    </tr>

                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold"><img src="../../images/LTC.png"
                                alt="Coin Icon" class="w-10"> Litecoin</td>
                        <td><span id="LTC_balance"><?php echo $ltcBalance; ?> LTC</td>
                        <td>$<?php echo number_format($ltcUsd, 2); ?> USD</td>

                    </tr>
                    <!-- Bitcoin Cash -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/BCH.png" alt="Coin Icon" class="w-10"> Bitcoin Cash
                        </td>
                        <td><span id="BCH_balance"><?php echo $bchBalance; ?> BCH</span></td>
                        <td>$<?php echo number_format($bchUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Ethereum Classic -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/ETC.png" alt="Coin Icon" class="w-10"> Ethereum Classic
                        </td>
                        <td><span id="ETC_balance"><?php echo $etcBalance; ?> ETC</span></td>
                        <td>$<?php echo number_format($etcUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Uniswap -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/UNI.png" alt="Coin Icon" class="w-10"> Uniswap
                        </td>
                        <td><span id="UNI_balance"><?php echo $uniBalance; ?> UNI</span></td>
                        <td>$<?php echo number_format($uniUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Chainlink -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/LINK.png" alt="Coin Icon" class="w-10"> Chainlink
                        </td>
                        <td><span id="LINK_balance"><?php echo $linkBalance; ?> LINK</span></td>
                        <td>$<?php echo number_format($linkUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Avalanche -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/AVAX.png" alt="Coin Icon" class="w-10"> Avalanche
                        </td>
                        <td><span id="AVAX_balance"><?php echo $avaxBalance; ?> AVAX</span></td>
                        <td>$<?php echo number_format($avaxUsd, 2); ?> USD</td>
                    </tr>

                    <!-- NEO -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/NEO.png" alt="Coin Icon" class="w-10"> NEO
                        </td>
                        <td><span id="NEO_balance"><?php echo $neoBalance; ?> NEO</span></td>
                        <td>$<?php echo number_format($neoUsd, 2); ?> USD</td>
                    </tr>

                    <!-- EOS -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/EOS.png" alt="Coin Icon" class="w-10"> EOS
                        </td>
                        <td><span id="EOS_balance"><?php echo $eosBalance; ?> EOS</span></td>
                        <td>$<?php echo number_format($eosUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Arbitrum -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/ARB.png" alt="Coin Icon" class="w-10"> Arbitrum
                        </td>
                        <td><span id="ARB_balance"><?php echo $arbBalance; ?> ARB</span></td>
                        <td>$<?php echo number_format($arbUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Aptos -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/APT.png" alt="Coin Icon" class="w-10"> Aptos
                        </td>
                        <td><span id="APT_balance"><?php echo $aptBalance; ?> APT</span></td>
                        <td>$<?php echo number_format($aptUsd, 2); ?> USD</td>
                    </tr>

                    <!-- Toncoin -->
                    <tr class="text-right table-row">
                        <td class="text-left flex gap-3 items-center font-bold">
                            <img src="../../images/TON.png" alt="Coin Icon" class="w-10"> Toncoin
                        </td>
                        <td><span id="TON_balance"><?php echo $tonBalance; ?> TON</span></td>
                        <td>$<?php echo number_format($tonUsd, 2); ?> USD</td>
                    </tr>

                </tbody>
            </table>
            <br>
        </div>
    </main>

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
</body>

</html>