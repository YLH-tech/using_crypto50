<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
        header("Location: withdrawal.php"); // Refresh the page after clearing
        exit();
    } else {
        // Handle any errors
        $errorMessage = "Failed to clear history.";
    }
}
// Fetch all withdrawal requests
$stmt = $pdo->prepare("SELECT * FROM withdrawal_requests WHERE hidden_user = 0 ORDER BY created_at DESC");
$stmt->execute();
$withdrawalHistory = $stmt->fetchAll();

?>
<p>USDT: <span id="USDT_balance"><?php echo number_format($balance['USDT'], 4); ?></span></p>
<p>BTC: <span id="BTC_balance"><?php echo number_format($balance['BTC'], 4); ?></span></p>
<p>ETH: <span id="ETH_balance"><?php echo number_format($balance['ETH'], 4); ?></span></p>

<p>USDC: <span id="USDC_balance"><?php echo number_format($balance['USDC'], 4); ?></span></p>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds</title>
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
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
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
    </style>
</head>
<body>
    <form method="POST" id="withdrawalForm" action="process_withdrawal.php">
        <label>Coin:</label>
        <select name="coin" id="coin" required onchange="updateBalance()">
            <option value="USDT">USDT</option>
            <option value="BTC">BTC</option>
            <option value="ETH">ETH</option>
            <option value="USDC">USDC</option>
        </select>
        <label>Wallet Address:</label>
        <input type="text" name="wallet_address" id="wallet_address" placeholder="Wallet Address" required>
        
        <label>Amount:</label>
        <input type="number" name="amount" id="amount" placeholder="Amount" step="0.0001" required oninput="calculateServiceCharge()">
        <button type="button" onclick="setMaxAmount()">All</button>
        
        <p>Service Charge: <span id="serviceCharge">0.0000</span></p>
        <p>Available Amount: <span id="availableAfterDeduction">0.0000</span></p>

        <p id="errorMessage" style="color: red;"></p>
        
        <!-- Withdraw button triggers validation and then modal if no errors -->
        <button type="button" onclick="validateAndShowModal()">Withdraw</button>
    </form>

    <!-- Modal for entering fund password -->
    <div id="fundPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeFundPasswordModal()">&times;</span>
            <h2>Enter Fund Password</h2>
            <input type="password" id="fundPassword" placeholder="Fund Password" required>
            <button type="button" onclick="submitWithdrawal()">Submit</button>
        </div>
    </div>
    <h3>Withdrawal History</h3>
    <form method="post">
        <button type="submit" name="clear_history_user" onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear History</button>
    </form><br>
    
    <table border="1">
        <tr>
            <th>Date</th>
            <th>Coin</th>
            <th>Amount</th>
            <th>Wallet Address</th>
            <th>Service Charge</th>
            <th>Net Amount</th>
            <th>Status</th>
        </tr>
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
    </table>

    <script>
        const balances = {
            USDT: <?php echo $balance['USDT']; ?>,
            BTC: <?php echo $balance['BTC']; ?>,
            ETH: <?php echo $balance['ETH']; ?>,
            USDC: <?php echo $balance['USDC']; ?>
        };

        function calculateServiceCharge() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const serviceCharge = 0.01 * amount;
            const netAmount = amount - serviceCharge;

            document.getElementById('serviceCharge').textContent = serviceCharge.toFixed(4);
            document.getElementById('availableAfterDeduction').textContent = netAmount.toFixed(4);
        }

        function updateBalance() {
            document.getElementById('amount').value = '';
            calculateServiceCharge(); 
        }

        function setMaxAmount() {
            const selectedCoin = document.getElementById('coin').value;
            const maxAmount = balances[selectedCoin];
            document.getElementById('amount').value = maxAmount.toFixed(4);
            calculateServiceCharge(); 
        }

        function validateAndShowModal() {
            const coin = document.getElementById('coin').value;
            const amount = parseFloat(document.getElementById('amount').value);
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
