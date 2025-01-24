<?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "", "project");

if ($conn === false) {
    die("Error: " . mysqli_connect_error());
}

// Fetch the user's ID from the query string
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user details and balances
    $query = "SELECT u.username, b.* 
              FROM users u 
              LEFT JOIN user_balances b ON u.id = b.user_id
              WHERE u.id = $user_id";

    $result = mysqli_query($conn, $query);

    // Check if the user exists
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        die("User not found.");
    }

    // Handle form submission for updating balances
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $balances = $_POST['balances'];

        $update_query = "UPDATE user_balances SET 
                USDT = '{$balances['USDT']}', 
                BTC = '{$balances['BTC']}', 
                ETH = '{$balances['ETH']}', 
                USDC = '{$balances['USDC']}', 
                BNB = '{$balances['BNB']}',
                XRP = '{$balances['XRP']}',
                DOGE = '{$balances['DOGE']}',
                SOL = '{$balances['SOL']}',
                ADA = '{$balances['ADA']}',
                TRX = '{$balances['TRX']}',
                DOT = '{$balances['DOT']}',
                LTC = '{$balances['LTC']}',
                BCH = '{$balances['BCH']}',
                ETC = '{$balances['ETC']}',
                UNI = '{$balances['UNI']}',
                LINK = '{$balances['LINK']}',
                AVAX = '{$balances['AVAX']}',
                NEO = '{$balances['NEO']}',
                EOS = '{$balances['EOS']}',
                ARB = '{$balances['ARB']}',
                APT = '{$balances['APT']}',
                TON = '{$balances['TON']}'
                WHERE user_id = $user_id";

        mysqli_query($conn, $update_query);

        // Redirect with success message in the query string
    header("location:admin_manage_balance.php?user_id=$user_id&successMsg=" . urlencode("Balances updated successfully!"));
    exit(); 
    }
} else {
    die("Invalid or missing user ID.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Manage User Balance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        h1 {
            margin-left: 330px;
            color:grey;
        }
        .naming {
            color: #333;
        }

        .message {
            margin: 20px auto;
            padding: 10px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }

        .balance-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .balance-card {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .balance-card strong {
            display: block;
            font-size: 20px;
            margin-bottom: 10px;
            color:mediumblue;
        }

        .balance-card input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            box-sizing: border-box;
        }

        .save-button {
            display: block;
            margin-left: 330px;
            padding: 15px 30px;
            font-size: 25px;
            background-color:springgreen;
            color:mediumblue;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .save-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <h1>Manage Balances</h1>
    <h1>The user name is <strong class="naming"><?php echo $user['username']; ?></strong></h1>
    <?php if (isset($_GET['successMsg'])): ?>
        <div class="message">
            <?php echo htmlspecialchars($_GET['successMsg']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="balance-container">
            <?php
            $coins = [
                'USDT', 'BTC', 'ETH', 'USDC', 'BNB',
                'XRP', 'DOGE', 'SOL', 'ADA', 'TRX',
                'DOT', 'LTC', 'BCH', 'ETC', 'UNI',
                'LINK', 'AVAX', 'NEO', 'EOS', 'ARB',
                'APT', 'TON'
            ];

            foreach ($coins as $coin): ?>
                <div class="balance-card">
                    <strong><?php echo $coin; ?></strong>
                    <input
                        type="number"
                        name="balances[<?php echo $coin; ?>]"
                        value="<?php echo $user[$coin] ?? 0; ?>"
                        step="0.000001">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="save-button">Save Changes</button>
    </form>
</body>

</html>
