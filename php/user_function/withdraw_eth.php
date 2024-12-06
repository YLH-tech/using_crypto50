<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetch();

// Clear history if the admin clicks "Clear"
if (isset($_POST['clear_history_user'])) {
    $stmt = $pdo->prepare("UPDATE withdrawal_requests SET hidden_user = 1"); // Only hide for admin, keep visible for user
    if ($stmt->execute()) {
        // If the update was successful, redirect to the admin page
        header("Location: withdraw_eth.php"); // Refresh the page after clearing
        exit();
    } else {
        // Handle any errors
        $errorMessage = "Failed to clear history.";
    }
}
// Fetch all withdrawal requests
$stmt = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE user_id = ? AND hidden_user = 0 ORDER BY created_at DESC");
$stmt->execute([$userId]);  // Bind the session user's ID
$withdrawalHistory = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>

    <!-- Style link -->
    <link rel="stylesheet" href="../../style/deposit_withdraw.css">

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid var(--iconic-color--);
            width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        .success {
            background: #F2DEDE;
            color: green;
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <!-- Withdraw main section -->
    <main>
        <h1 class="text-5xl my-5">Withdraw</h1>
        <?php if (isset($_SESSION['successMsg'])): ?>

            <div class="success">
                <?php
                echo $_SESSION['successMsg'];
                unset($_SESSION['successMsg']);
                ?>
            </div>
        <?php endif ?>

       <!-- Main Section -->
       <div class="main-section">
            <!-- Sub Container -->
            <div class="main-container withdraw-container">
                <!-- Showing amount -->
            <div class="showing-amount">
                <h1>Service Charge : </h1><span id="serviceCharge">0.000000</span>
                <br>
                <h1>Avaliable Amount : </h1> <span id="availableAfterDeduction">0.000000</span>
            </div>
                <br>
                <!-- Processing div -->
                <form method="POST" id="withdrawalForm" action="process_withdrawal_btc.php">
                    <div class="sub-container">
                        <ol class="list-decimal text-2xl">
                            <li hidden>
                                <label for="coin-select" class="text-2xl">Select Coin/ Token</label>
                                <br>
                                <select name="coin" id="coin" class="data-inputs" required onchange="updateBalance()">
                                    <option value="ETH">ETH</option>
                                </select>
                            </li>
                            <br>
                            <li>
                                <label for="wallet-add" class="text-2xl">Wallet Address</label>
                                <br>
                                <input type="text" name="wallet_address" id="wallet_address" class="data-inputs" placeholder="Enter wallet address" required>
                            </li>
                            <br>
                            <li>
                                <h3 class="text-2xl">Withdraw Amount</h3>
                                <p class="text-[18px]">Select Network</p>
    
                                <!-- Input section -->
                                <div class="input-section">
                                    <!-- For showing selected coin -->
                                    <h3 class="selected-coin flex items-center gap-2 bg-grey-custom w-[300px]">
                                        <img src="../../images/ETH.png" class="w-8 dynamic-image" alt="ETH">Ethereum
                                    </h3>
    
                                    <!-- For input amount -->
                                    <input type="number" name="amount" id="coin-amount" class="bg-grey-custom" placeholder="Amount" step="0.0001" required oninput="calculateServiceCharge()">
    
                                    <button type="button" class="all-btn" onclick="setMaxAmount()">All</button>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <p id="errorMessage" style="color: red;"></p>
                    <br><br>
                    <button type="button" class="text-2xl req-btn" onclick="validateAndShowModal()">Withdraw</button>
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
        </div>
    </main>
    <br><br><br>

    <!-- Modal for entering fund password -->
    <div id="fundPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFundPasswordModal()">&times;</span>
            <h1 class="text-3xl">Enter Fund Password</h1>

            <input type="password" class="data-inputs" id="fundPassword" placeholder="Fund Password" required>
            <br><br>
            <button type="button" class="text-2xl req-btn" onclick="submitWithdrawal()">Submit</button>
        </div>
    </div>
    <!-- Transaction History -->
    <section class="transaction-history">
        <h1 class="text-3xl">Withdraw History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white my-3" name="clear_history_user" onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear History</button>
        </form>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th class="rounded-l-md">Date</th>
                    <th>Coin</th>
                    <th>Amount</th>
                    <th>Wallet Address</th>
                    <th>Service Charge</th>
                    <th>Net Amount</th>
                    <th class="rounded-r-md">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($withdrawalHistory as $request): ?>
                    <tr>
                        <td><?php echo $request['created_at']; ?></td>
                        <td><?php echo htmlspecialchars($request['coin']); ?></td>
                        <td><?php echo number_format($request['amount'], 4); ?></td>
                        <td><?php echo htmlspecialchars($request['wallet_address']); ?></td>
                        <td><?php echo number_format($request['service_charge'], 4); ?></td>
                        <td><?php echo number_format($request['net_amount'], 4); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <script>
        const balances = {
            USDT: <?php echo $balance['USDT']; ?>,
            BTC: <?php echo $balance['BTC']; ?>,
            ETH: <?php echo $balance['ETH']; ?>,
            USDC: <?php echo $balance['USDC']; ?>
        };

        function calculateServiceCharge() {
            const amount = parseFloat(document.getElementById('coin-amount').value) || 0;
            const serviceCharge = 0.01 * amount;
            const netAmount = amount - serviceCharge;

            document.getElementById('serviceCharge').textContent = serviceCharge.toFixed(6);
            document.getElementById('availableAfterDeduction').textContent = netAmount.toFixed(6);
        }

        function updateBalance() {
            document.getElementById('coin-amount').value = '';
            calculateServiceCharge();
        }

        function setMaxAmount() {
            const selectedCoin = document.getElementById('coin').value;
            const maxAmount = balances[selectedCoin];
            document.getElementById('coin-amount').value = maxAmount.toFixed(6);
            calculateServiceCharge();
        }

        function validateAndShowModal() {
            const coin = document.getElementById('coin').value;
            const amount = parseFloat(document.getElementById('coin-amount').value);
            const walletAddress = document.getElementById('wallet_address').value;
            const errorMessage = document.getElementById('errorMessage');

            errorMessage.textContent = '';

            if (!walletAddress) {
                errorMessage.textContent = "Please enter a wallet address.";
                return;
            }

            if (isNaN(amount) || amount <= 0) {
                errorMessage.textContent = "Withdrawal amount must be greater than zero.";
                return;
            }

            if (balances[coin] < amount) {
                errorMessage.textContent = "Insufficient balance for this withdrawal.";
                return;
            }

            // If no errors, show the password modal
            showFundPasswordModal();
        }

        function showFundPasswordModal() {
            document.getElementById('fundPasswordModal').style.display = 'block';
        }

        function closeFundPasswordModal() {
            document.getElementById('fundPasswordModal').style.display = 'none';
        }

        function submitWithdrawal() {
            const fundPassword = document.getElementById('fundPassword').value;

            if (!fundPassword) {
                alert("Please enter the fund password.");
                return;
            }

            const fundPasswordInput = document.createElement('input');
            fundPasswordInput.type = 'hidden';
            fundPasswordInput.name = 'fund_password';
            fundPasswordInput.value = fundPassword;

            const form = document.getElementById('withdrawalForm');
            form.appendChild(fundPasswordInput);

            form.submit();
        }
    </script>

</body>

</html>