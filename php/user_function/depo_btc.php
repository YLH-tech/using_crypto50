<?php
session_start();
include '../database/db.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Clear history if the user clicks "Clear"
if (isset($_POST['clear_history_user'])) {
    $stmt = $pdo->prepare("UPDATE transactions SET hidden_user = 1 WHERE user_id = ? "); // Only hide for user, keep visible for admin
    $stmt->execute([$user_id]);
    header("Location: depo_btc.php"); // Refresh the page after clearing
    exit();
}

// Fetch user balance
$stmt = $pdo->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetch();

// Fetch transaction history (only records visible to user)
$history_stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND hidden_user = 0 ORDER BY timestamp DESC");
$history_stmt->execute([$user_id]);
$transactions = $history_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>

    <!-- Style links -->
   
    <link rel="stylesheet" href="../../style/deposit_withdraw.css">
    

    <!-- Tailwind CSS link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


    <!-- Fontawesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Uploading File -->
    <script src="../../js/fileUpload.js"></script>
    <style>
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


    <br>

    <!-- Deposite main section -->
    <main>
        <h1 class="text-5xl my-5">Deposit</h1>
        <?php if (isset($_SESSION['successMsg'])): ?>

            <div class="success">
                <?php
                echo $_SESSION['successMsg'];
                unset($_SESSION['successMsg']);
                ?>
            </div>
        <?php endif ?>

        <div class="main-section">
            <!-- Sub Container -->
            <div class="main-container">
                <!-- QR container -->
                <div class="qr-container">
                    <img src="../../images/BTC_coin_QR.jpg" alt="BTC Coin QR" class="qr-img">
                    <p class="qr-explain">Please, Scan the QR for BTC Wallet Address</p>
                    <br>
                    <!-- Wallet Code -->
                    <div class="m-auto w-fit">
                        <input type="text" value="bc1q6kl48f064md7tl2jsje3y24rsk2ps96e86edn2" id="wallet-code"
                            class="code-container" disabled>
                        <button class="copy-btn" onclick="code_copying()"><i
                                class="fa-solid fa-copy text-2xl"></i></button>
                    </div>
                </div>

                <ol class="list-decimal text-2xl">
                    <!-- Processing div -->
                    <form action="../../admin/req_coin_btc.php" method="post" enctype="multipart/form-data">
                        <div class="sub-container">
                            <li hidden>
                                <h3 class="text-2xl mb-5"></h3>
                                <select name="coin_type" id="coin-select" class="data-inputs">

                                    <option value="BTC">BTC</option>

                                </select>
                            </li><br>

                            <li>
                                <h3 class="text-2xl my-5">Generate Deposit Address</h3>
                                <p class="text-[18px]">Select Network</p>

                                <!-- Input section -->
                                <div class="input-section">
                                    <!-- For showing selected coin -->
                                    <h3 class="selected-coin flex items-center gap-2 bg-grey-custom w-[300px]">
                                        <img src="../../images/BTC.png" class="w-8" alt="BTC">Bitcoin
                                    </h3><br><br>

                                    <!-- For input amount -->
                                    <input class="bg-grey-custom" placeholder="Enter amount" id="coin-amount"
                                        type="number" step="0.0001" name="amount" required>


                                    <input type="file" name="image" accept=".jpeg,.jpg,.png,.heic" id="file-upload"
                                        onchange="showUploadedFile()" hidden>

                                </div><br>
                            </li>
                        </div>
                        <br><br>
                        <input type="submit" id="request-btn" class="text-2xl req-btn" value="Request" hidden>

                    </form>

                    <li>
                        <!-- Image preview area -->
                        <div class="card">
                            <div class="drop_box" id="drop-box">
                                <!-- Image preview area (hidden initially) -->
                                <img id="imagePreview" src="#" alt="Selected Image"
                                    style="display: none; max-width: 400px; margin-top: 10px;">

                                <!-- Hidden input for file upload -->
                                <input type="file" accept=".jpeg,.jpg,.png,.heic" id="file-upload"
                                    onchange="showUploadedFile()" hidden>

                                <!-- Instructions text -->
                                <header id="instructions">
                                    <h4>Select File here to verify.</h4>
                                    <p>Files Supported: .JPEG, .JPG, .PNG, .HEIC</p>
                                </header>
                                <!-- "Choose File" button -->
                                <h3 class="btn" id="upload-btn">Chose File</h3>
                            </div>
                        </div>
                    </li>
                    <br>
                    <button class="req-btn" onclick="document.getElementById('request-btn').click();">Request</button>
                </ol>
            </div>

            <!-- Your Balance -->
            <div class="coin-price" style="height: 1020px;">
                <h2 class="text-4xl">Your Balance</h2>
                <br>
                <!-- Table for coin price -->
                <table class="coin-table">
                    <thead>
                        <tr>
                            <th class="text-left text-2xl">Coin</th>
                            <th class="text-right text-2xl">Price</th>
                        </tr>
                    </thead>
                    <tbody class="scrollable-area h-[88%]">
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
                            <td class="coins"><img src="../../images/AVAX.png" alt="AVA icon"> AVAX: </td>
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
    <!-- Transaction History -->
    <section class="transaction-history">
        <h1 class="text-3xl">Transaction History</h1>
        <form method="post">
            <button type="submit" class="bg-red-400 p-2 rounded-md text-white my-3" name="clear_history_user" onclick="return confirm('Are you sure you want to clear your transaction history?');">Clear History</button>
        </form>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <th class="rounded-l-md">Action</th>
                    <th>Coin Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Admin Note</th>
                    <th class="rounded-r-md">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= ucfirst($transaction['action']) ?></td>
                        <td><?= strtoupper($transaction['coin_type']) ?></td>
                        <td><?= $transaction['amount'] ?></td>
                        <td><?= ucfirst($transaction['status']) ?></td>
                        <td><?= nl2br($transaction['admin_note']) ?></td>
                        <td><?= $transaction['timestamp'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>


    <!-- Script functions -->
    <script src="../../js/dropdownScript.js"></script> <!-- drop down btn script -->

    <script>
        $('#upload-btn').click(function() {
            $('#file-upload').click();
        });

        function code_copying() {
            // Get the text field
            var copyText = document.getElementById("wallet-code");

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);

            // Alert the copied text
            alert("Copied the btc code : " + copyText.value);
        }
    </script>

</body>

</html>