<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';
include 'get_real_time_prices.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['clear_record'])) {
    $clear_stmt = $conn->prepare("DELETE FROM transactions_exchange WHERE user_id = ?");
    $clear_stmt->execute([$userId]); // Use $userId instead of $user_id
    header("Location: exchangePrice.php"); // Refresh the page
    exit();
}


// Fetch user's current balances for displaying
$sql = "SELECT USDT, BTC, ETH,USDC, BNB, XRP, DOGE, SOL, ADA, TRX, DOT, LTC, BCH, ETC, UNI, LINK, AVAX, NEO, EOS, ARB, APT, TON FROM user_balances WHERE user_id = $userId";
$balanceResult = $conn->query($sql);
$balance = $balanceResult->fetch_assoc();


// Fetch user's exchange records
$recordsSql = "SELECT from_coin, to_coin, from_amount, to_amount, rate, transaction_date 
                FROM transactions_exchange WHERE user_id = $userId ORDER BY transaction_date DESC";
$recordsResult = $conn->query($recordsSql);

// Direct USD estimation
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
        .success {
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
    
    <h2 class="text-center text-4xl">Exchange Coins</h2>
    <br>
    <p class="text-center sec-font-color">Swap assets effortlessly and securely with CoinEX's self-developed algorithm
    </p>
    <br>

    <!-- Main Section -->
    <main class="main-section">
        <!-- Exchange section -->
        <div class="main-container">
        <?php if (isset($_SESSION['successMsg'])): ?>

            <div class="success">
                <?php
                echo $_SESSION['successMsg'];
                unset($_SESSION['successMsg']);
                ?>
            </div>
        <?php endif ?>
        <form method="POST" action="exchange.php">
            <div class="horizontal-container">
                <div>
                    <h1>Exchange Rate</h1>
                    <span id="exchange-rate"></span>
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
                <br>
                <br>
                <!-- Amount input -->
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


    <!-- Swapping Function -->
    <script src="./js/dropdownScript.js"></script> <!-- drop down btn script -->

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
        // Function to get the maximum balance for the selected coin and set it to the input field
        function setMaxAmount() {
            // Get the selected coin from the dropdown
            var selectedCoin = document.getElementById('from_coin').value;

            // Get the balance of the selected coin from coinData
            var balance = parseFloat(coinData[selectedCoin].balance) || 0;

            // Set the input field to the maximum amount
            document.getElementById('amountInput').value = balance.toFixed(6); // Show 6 decimal places

            // Update the available amount and other info after setting the max amount
            updateCoinInfo();
        }

        // Function to clear the input field when the coin selection changes
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

            // Update the coin information after the swap
            updateCoinInfo();
        }

        function clearInput() {
            document.getElementById('amountInput').value = ''; // Clear the input field
        }

        // Simulated data for coin information
        const coinData = {
            USDT: {
                rate: "<?php echo number_format($usdtPrice, 6); ?>",
                balance: "<?php echo $usdtBalance; ?> ",
                available: 0
            },
            BTC: {
                rate: "<?php echo number_format($btcPrice, 6); ?>",
                balance: "<?php echo $btcBalance; ?> ",
                available: 0
            },
            ETH: {
                rate: "<?php echo number_format($ethPrice, 6); ?>",
                balance: "<?php echo $ethBalance; ?> ",
                available: 0
            },
            BNB: {
                rate: "<?php echo number_format($bnbPrice, 6); ?>",
                balance: "<?php echo $bnbBalance; ?>",
                available: 0
            },
            USDC: {
                rate: "<?php echo number_format($usdcPrice, 6); ?>",
                balance: "<?php echo $usdcBalance; ?>",
                available: 0
            },
            XRP: {
                rate: "<?php echo number_format($xrpPrice, 6); ?>",
                balance: "<?php echo $xrpBalance; ?>",
                available: 0
            },
            DOGE: {
                rate: "<?php echo number_format($dogePrice, 6); ?>",
                balance: "<?php echo $dogeBalance; ?>",
                available: 0
            },
            SOL: {
                rate: "<?php echo number_format($solPrice, 6); ?>",
                balance: "<?php echo $solBalance; ?>",
                available: 0
            },
            ADA: {
                rate: "<?php echo number_format($adaPrice, 6); ?>",
                balance: "<?php echo $adaBalance; ?>",
                available: 0
            },
            TRX: {
                rate: "<?php echo number_format($trxPrice, 6); ?>",
                balance: "<?php echo $trxBalance; ?>",
                available: 0
            },
            DOT: {
                rate: "<?php echo number_format($dotPrice, 6); ?>",
                balance: "<?php echo $dotBalance; ?>",
                available: 0
            },
            LTC: {
                rate: "<?php echo number_format($ltcPrice, 6); ?>",
                balance: "<?php echo $ltcBalance; ?>",
                available: 0
            },
            BCH: {
                rate: "<?php echo number_format($bchPrice, 6); ?>",
                balance: "<?php echo $bchBalance; ?>",
                available: 0
            },
            ETC: {
                rate: "<?php echo number_format($etcPrice, 6); ?>",
                balance: "<?php echo $etcBalance; ?>",
                available: 0
            },
            UNI: {
                rate: "<?php echo number_format($uniPrice, 6); ?>",
                balance: "<?php echo $uniBalance; ?>",
                available: 0
            },
            LINK: {
                rate: "<?php echo number_format($linkPrice, 6); ?>",
                balance: "<?php echo $linkBalance; ?>",
                available: 0
            },
            AVAX: {
                rate: "<?php echo number_format($avaxPrice, 6); ?>",
                balance: "<?php echo $avaxBalance; ?>",
                available: 0
            },
            NEO: {
                rate: "<?php echo number_format($neoPrice, 6); ?>",
                balance: "<?php echo $neoBalance; ?>",
                available: 0
            },
            EOS: {
                rate: "<?php echo number_format($eosPrice, 6); ?>",
                balance: "<?php echo $eosBalance; ?>",
                available: 0
            },
            ARB: {
                rate: "<?php echo number_format($arbPrice, 6); ?>",
                balance: "<?php echo $arbBalance; ?>",
                available: 0
            },
            APT: {
                rate: "<?php echo number_format($aptPrice, 6); ?>",
                balance: "<?php echo $aptBalance; ?>",
                available: 0
            },
            TON: {
                rate: "<?php echo number_format($tonPrice, 6); ?>",
                balance: "<?php echo $tonBalance; ?>",
                available: 0
            }
        };

        function updateCoinInfo() {
            const fromCoin = document.getElementById("from_coin").value;
            const toCoin = document.getElementById("to_coin").value;
            const amountInput = document.getElementById("amountInput").value || 0;

            // Get coin rates from predefined data
            const fromRate = parseFloat(coinData[fromCoin]?.rate.replace(/,/g, '')) || 1;
            const toRate = parseFloat(coinData[toCoin]?.rate.replace(/,/g, '')) || 1;

            let exchangeRate;

            // Determine exchange rate based on direction
            if (fromCoin === "USDT") {
                // From USDT to another coin
                exchangeRate = 1 / toRate;
            } else if (toCoin === "USDT") {
                // From another coin to USDT
                exchangeRate = fromRate;
            } else {
                // Between two non-USDT coins
                exchangeRate = (1 / fromRate) * toRate;
            }

            // Display the exchange rate
            document.getElementById("exchange-rate").textContent = `${exchangeRate.toFixed(6)} ${toCoin}`;

            // Show the user's balance for the "From Coin"
            document.getElementById("balance").textContent = `${coinData[fromCoin]?.balance || 0} ${fromCoin}`;

            // Calculate the equivalent "To Coin" amount
            const calculatedAmount = (amountInput * exchangeRate).toFixed(6);
            document.getElementById("available-amount").textContent = `${calculatedAmount} ${toCoin}`;
        }

        // Event listener to update the exchange rate and calculated amount on input change
        document.getElementById("from_coin").addEventListener("change", updateCoinInfo);
        document.getElementById("to_coin").addEventListener("change", updateCoinInfo);
        document.getElementById("amountInput").addEventListener("input", updateCoinInfo);

        // Initialize the values when the page loads
        updateCoinInfo();
    </script>
</body>

</html>