<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
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
$sql = "SELECT BTC, ETH, USDT, BND, USDC, DOGE, TRX, DOT, ADA, BSV, XRP, LTC, EOS, BCH, DF, QTUM, IOTA, NEO, NAS, ELA, SNT, WICC FROM user_balances WHERE user_id = $userId";
$balanceResult = $conn->query($sql);
$balance = $balanceResult->fetch_assoc();

// Fetch user's exchange records
$recordsSql = "SELECT from_coin, to_coin, from_amount, to_amount, rate, transaction_date 
               FROM transactions_exchange WHERE user_id = $userId ORDER BY transaction_date DESC";
$recordsResult = $conn->query($recordsSql);

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Exchange</title>

    <!-- Styles -->
    <link rel="stylesheet" href="./style/exchange.css">

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        // Function to get the maximum balance for the selected coin and set it to the input field
        function setMaxAmount() {
            // Get the selected coin from the dropdown
            var selectedCoin = document.getElementById('from_coin').value;

            // Get the balance of the selected coin
            var balance = parseFloat(document.getElementById(selectedCoin + '_balance').innerText);

            // Set the input field to the maximum amount
            document.getElementById('amountInput').value = balance.toFixed(4); // Show 4 decimal places
        }

        // Function to clear the input field when the coin selection changes
        function clearInput() {
            document.getElementById('amountInput').value = ''; // Clear the input field
        }
    </script>
</head>

<body>

    <h2>Your Balance</h2>
    <p>BTC: <span id="BTC_balance"><?php echo $balance['BTC']; ?></span></p>
<p>ETH: <span id="ETH_balance"><?php echo $balance['ETH']; ?></span></p>
<p>USDT: <span id="USDT_balance"><?php echo $balance['USDT']; ?></span></p>
<p>USDC: <span id="USDC_balance"><?php echo $balance['USDC']; ?></span></p>
<p>BND: <span id="BND_balance"><?php echo $balance['BND']; ?></span></p>

<p>DOGE: <span id="DOGE_balance"><?php echo $balance['DOGE']; ?></span></p>
<p>TRX: <span id="TRX_balance"><?php echo $balance['TRX']; ?></span></p>
<p>DOT: <span id="DOT_balance"><?php echo $balance['DOT']; ?></span></p>
<p>ADA: <span id="ADA_balance"><?php echo $balance['ADA']; ?></span></p>
<p>BSV: <span id="BSV_balance"><?php echo $balance['BSV']; ?></span></p>
<p>XRP: <span id="XRP_balance"><?php echo $balance['XRP']; ?></span></p>
<p>LTC: <span id="LTC_balance"><?php echo $balance['LTC']; ?></span></p>
<p>EOS: <span id="EOS_balance"><?php echo $balance['EOS']; ?></span></p>
<p>BCH: <span id="BCH_balance"><?php echo $balance['BCH']; ?></span></p>
<p>DF: <span id="DF_balance"><?php echo $balance['DF']; ?></span></p>
<p>QTUM: <span id="QTUM_balance"><?php echo $balance['QTUM']; ?></span></p>
<p>IOTA: <span id="IOTA_balance"><?php echo $balance['IOTA']; ?></span></p>
<p>NEO: <span id="NEO_balance"><?php echo $balance['NEO']; ?></span></p>
<p>NAS: <span id="NAS_balance"><?php echo $balance['NAS']; ?></span></p>
<p>ELA: <span id="ELA_balance"><?php echo $balance['ELA']; ?></span></p>
<p>SNT: <span id="SNT_balance"><?php echo $balance['SNT']; ?></span></p>
<p>WICC: <span id="WICC_balance"><?php echo $balance['WICC']; ?></span></p>

    <br><br>
    <h2 class="text-center text-4xl">Exchange Coins</h2>
    <br>
    <p class="text-center sec-font-color">Swap assets effortlessly and securely with CoinEX's self-developed algorithm
    </p>
    <br>
    <!-- Exchange section -->
    <div class="main-container">

        <form method="POST" action="./php/exchange.php">
            <!-- From coin -->
            <div class="from-coin-div">
                <label>From Coin:</label>
                <select name="from_coin" id="from_coin" required onchange="clearInput();">
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                    <option value="USDT">USDT</option>
                    <option value="USDC">USDC</option>
                    <option value="BND">BND</option>
                    <option value="DOGE">DOGE</option>
                    <option value="TRX">TRX</option>
                    <option value="DOT">DOT</option>
                    <option value="ADA">ADA</option>
                    <option value="BSV">BSV</option>
                    <option value="XRP">XRP</option>
                    <option value="LTC">LTC</option>
                    <option value="EOS">EOS</option>
                    <option value="BCH">BCH</option>
                    <option value="DF">DF</option>
                    <option value="QTUM">QTUM</option>
                    <option value="IOTA">IOTA</option>
                    <option value="NEO">NEO</option>
                    <option value="NAS">NAS</option>
                    <option value="ELA">ELA</option>
                    <option value="SNT">SNT</option>
                    <option value="WICC">WICC</option>
                </select>
                <br>
                <br>
                <!-- Amount input -->
                <div class="amount-input gap-5">
                    <input type="number" class="w-[100%] h-10 p-5" step="0.0001" name="amount" id="amountInput"
                        placeholder="Enter amount" required>
                    <a href="#" class="text-green-400 text-[20px]" onclick="setMaxAmount()">Max.</a>
                </div>
            </div>

            <br>
            <img src="./assets/images/transfer.png" alt="Exchange icon" class="w-10 m-auto cursor-pointer">
            <br>

            <!-- To coin -->
            <div class="to-coin-div">
                <label>To Coin:</label>
                <select name="to_coin" required>
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                    <option value="USDT">USDT</option>
                    <option value="USDC">USDC</option>
                    <option value="BND">BND</option>
                    <option value="DOGE">DOGE</option>
                    <option value="TRX">TRX</option>
                    <option value="DOT">DOT</option>
                    <option value="ADA">ADA</option>
                    <option value="BSV">BSV</option>
                    <option value="XRP">XRP</option>
                    <option value="LTC">LTC</option>
                    <option value="EOS">EOS</option>
                    <option value="BCH">BCH</option>
                    <option value="DF">DF</option>
                    <option value="QTUM">QTUM</option>
                    <option value="IOTA">IOTA</option>
                    <option value="NEO">NEO</option>
                    <option value="NAS">NAS</option>
                    <option value="ELA">ELA</option>
                    <option value="SNT">SNT</option>
                    <option value="WICC">WICC</option>
                </select>
            </div>
            <br>
            <button type="submit" class="w-[100%] h-12 bg-green-400 rounded-[50px]">Exchange</button>
        </form>
    </div>

    <!-- Exchange History -->
    <div class="exchange-history">
        <h2 class="text-2xl">Your Exchange Records</h2>
        <br>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white" name="clear_record"
                onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear
                Record</button>
        </form><br>
        <table border="1">
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
        </table>
    </div>

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

</body>

</html>