<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php'; // Include your database connection
require 'db_connection.php';  // Include the MySQLi connection file

$currentPasswordChangeTime = $_SESSION['last_password_change'];  // Store the last password change timestamp in session

// Query to fetch the last password change timestamp from the database
$query = "SELECT last_password_change FROM admin_users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    // Compare the stored timestamp with the one from the database
    if ($currentPasswordChangeTime !== $row['last_password_change']) {
        // If the timestamps don't match, the password has been changed, so force login again
        session_unset();  // Unset all session variables
        session_destroy();  // Destroy the session
        header("Location: admin_login.php?error=Password has been changed. Please log in again.");
        exit;  // Stop further execution
    }
}

// Fetch total count of orders for calculating percentages
$totalOrdersStmt = $pdo->query("SELECT COUNT(*) FROM orders");
$totalOrders = $totalOrdersStmt->fetchColumn() ?: 1; // Avoid division by zero

// Define trading pairs and fetch data from database
$pairs = [
    'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT', 
    'ADAUSDT', 'TRXUSDT', 'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT', 
    'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 'EOSUSDT', 'ARBUSDT', 
    'APTUSDT', 'TONUSDT'
];

$data = [];
foreach ($pairs as $pair) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE symbol = ?");
    $stmt->execute([$pair]);
    $count = $stmt->fetchColumn();
    $data[$pair] = $count;
}

$jsonData = json_encode($data);

// Define trade volume query function
function getTradeVolume($pair, $interval) {
    global $pdo;
    $query = "SELECT SUM(amount) 
              FROM orders 
              WHERE symbol = ? 
              AND created_at >= DATE_SUB(NOW(), INTERVAL 1 $interval)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pair]);
    return $stmt->fetchColumn() ?: 0;  // Ensure 0 is returned if no result
}


// Fetch volume data for all pairs for both weekly and monthly
$volumeData = ['weekly' => [], 'monthly' => []];
foreach ($pairs as $pair) {
    // Get trade volume for weekly and monthly
    $volumeData['weekly'][$pair] = getTradeVolume($pair, 'WEEK');
    $volumeData['monthly'][$pair] = getTradeVolume($pair, 'MONTH');
}

// Encode data for JavaScript use
$jsonVolumeData = json_encode($volumeData);

// Fetch the latest 10 transactions with the associated user name and allow status
$latestTransactionsStmt = $pdo->query("
    SELECT o.id, o.symbol, o.order_type, o.amount, o.starting_price, o.end_price, o.expected_pl, o.created_at, u.username, u.allow
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$latestTransactions = $latestTransactionsStmt->fetchAll();

// Fetch the latest 10 users including account status and trade allow columns
$latestUsersStmt = $pdo->query("SELECT id, username, created_at, email, status, allow FROM users ORDER BY created_at DESC LIMIT 10");
$latestUsers = $latestUsersStmt->fetchAll();

// Fetch the count of pending coin requests
$pending_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM coin_requests WHERE status = 'pending'");
$pending_requests_count = $pending_requests_count_stmt->fetch()['count'];
// Fetch the count of pending coin requests
$waiting_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM withdrawal_requests WHERE status = 'Waiting a few movement'");
$waiting_requests_count = $waiting_requests_count_stmt->fetch()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<script src="js/chart.umd.js"></script>

    <script>
        function toggleSidebar() {
            document.addEventListener('DOMContentLoaded', function () {
                // Add event listener to hamburger button after DOM is fully loaded
                document.querySelector('.hamburger').addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                    this.classList.toggle('active'); // Optional: Toggle hamburger icon state
                });
            });

        }
    </script>


    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="user_management.php"><i class="fa fa-users"></i> User Management</a></li>
            <li><a href="order_dashboard.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
            <li>
                <a href="deposit_management.php">
                    <i class="fa fa-wallet"></i> Deposits
                    <span class="badge"><?= $pending_requests_count > 0 ? $pending_requests_count : 0 ?></span>
                </a>
            </li>
            <li>
                <a href="withdraw_management.php">
                    <i class="fa fa-money-check-alt"></i> Withdraws
                    <span class="badge"><?= $waiting_requests_count > 0 ? $waiting_requests_count : 0 ?></span>
                </a>
            </li>
            <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
            <!-- Logout Confirmation Modal -->
            <div id="logoutModal" class="modal">
                <div class="modal-content">
                    <h3>Are you sure you want to log out?</h3>
                    <div class="modal-buttons">
                        <button id="confirmLogout">OK</button>
                        <button id="cancelLogout">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</a></li>

        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header-wrapper">
            <!-- Hamburger button -->
            <div class="hamburger" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
            <h2>Overview Dashboard</h2>
            <br>
        </div>

        <section class="dashboard-cards">
            <div class="card" data-update="users">
                <h3>Total Users</h3>
                <p id="total-users">0</p>
            </div>
            <div class="card" data-update="balance">
                <h3>Total Balances (USDT)</h3>
                <p id="total-balance">0.000000</p>
            </div>
            <div class="card" data-update="transactions">
                <h3>Total Transactions</h3>
                <p id="total-transactions">0</p>
            </div>
        </section>


        <!-- Chart Section with Left Alignment for Pie Chart -->
        <div class="chart-container" style="display: flex; align-items: center;">
            <div style="flex: 1;">
                <!-- Removed redundant <h2> title -->
                <canvas id="tradingPairsChart" width="250" height="250"></canvas>
            </div>
            <div style="flex: 1;">
                <!-- Volume Chart -->
                <canvas id="volumeChart" width="400" height="350"></canvas>

                 <!-- Filter for Volume Type -->
                 <div class="filter-container">
                    <label for="volumeFilter">Select Volume:</label>
                    <select id="volumeFilter">
                        <option value="monthly">Monthly Trade Volume</option>
                        <option value="weekly">Weekly Trade Volume</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Latest Transactions and User Registrations Section -->
        <div class="tables-section">
            <!-- Latest Transactions Table -->
            <div class="table-container-left">
                <h3>Latest Orders</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Username</th>
                            <th>Symbol</th>
                            <th>Order Type</th>
                            <th>Amount</th>
                            <th>Starting Price</th>
                            <th>End Price</th>
                            <th>Profit/Loss</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestTransactions as $transaction): ?>
                            <tr>
                                <td><?php echo $transaction['id']; ?></td>
                                <td><?php echo $transaction['symbol']; ?></td>
                                <td><?php echo ucfirst($transaction['order_type']); ?></td>
                                <td><?php echo number_format($transaction['amount'], 6); ?></td>
                                <td><?php echo number_format($transaction['starting_price'], 2); ?></td>
                                <td><?php echo number_format($transaction['end_price'], 2); ?></td>
                                <td><?php echo number_format($transaction['expected_pl'], 2); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($transaction['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Latest User Registrations Table -->
            <div class="table-container-right">
                <h3>Latest User Registrations</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Account Status</th>
                            <th>Trade Allow</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestUsers as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['status']; ?></td>
                                <td><?php echo $user['allow']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- This is for Total users, total balances, total transactions -->
    <script>
        // Function to animate counter change
        function animateCounter(element, targetValue, decimals = 0) {
            const duration = 1000; // Animation duration in ms
            let start = parseFloat(element.textContent.replace(/,/g, ''));
            let startTime = performance.now();

            function updateCounter(currentTime) {
                let progress = Math.min((currentTime - startTime) / duration, 1);
                let currentValue = start + (targetValue - start) * progress;
                element.textContent = currentValue.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            requestAnimationFrame(updateCounter);
        }

        // Fetch updated counts from the server
        function fetchUpdates() {
            fetch('fetch_updates.php')
                .then(response => response.json())
                .then(data => {
                    animateCounter(document.getElementById('total-users'), data.totalUsers);
                    animateCounter(document.getElementById('total-balance'), data.totalBalance, 6);
                    animateCounter(document.getElementById('total-transactions'), data.totalTransactions);
                });
        }

        // Update every 10 seconds
        setInterval(fetchUpdates, 10000);
        fetchUpdates(); // Initial fetch

    </script>

    <!-- This is for pie chart  -->
    <script>
        // Fetch data from PHP
        const data = <?php echo $jsonData; ?>;
        const totalOrders = <?php echo $totalOrders; ?>;
        const labels = Object.keys(data);
        const counts = Object.values(data);

        // Calculate percentages
        const values = counts.map(count => ((count / totalOrders) * 100).toFixed(2));

        // Render Pie Chart with Updated Colors and Title
        new Chart(document.getElementById('tradingPairsChart'), {
            type: 'pie',
            data: {
                labels: labels.map((label, index) => `${label} (${counts[index]} | ${values[index]}%)`),
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        '#FF6F61', '#6B5B95', '#88B04B', '#F7CAC9', '#92A8D1',
                        '#F0B27A', '#FF8C00', '#D5A6BD', '#A6ACEC', '#FF4F58',
                        '#F1C40F', '#1ABC9C', '#E74C3C', '#9B59B6', '#3498DB',
                        '#E67E22', '#16A085', '#2C3E50', '#D35400', '#7D3C98'
                    ],
                    hoverOffset: 30 
                }]
            },
            options: {
                responsive: true,
                layout: {
                    padding: 10 
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Percentage of Traded Pairs',
                        font: {
                            size: window.innerWidth < 768 ? 20 : 30,  // Adjust font size based on screen width
                            weight: 'bold'
                        },
                        padding: {
                            top: 0,
                            bottom:60  // Increase bottom padding to create more space below the title
                        },
                        responsive: true,
                    },
                    legend: {
                        position: 'left',
                        labels: {
                            font: {
                                size: 10 // Adjust legend font size for better readability on small screens
                            },
                            padding: 5
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const count = counts[tooltipItem.dataIndex];
                                const percentage = ((count / totalOrders) * 100).toFixed(2);
                                return `${labels[tooltipItem.dataIndex]}: ${count} orders (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <!-- This is for monthly trade volume chart  -->
    <script>
        // Fetch volume data from PHP
        const volumeData = <?php echo $jsonVolumeData; ?>;
        let currentVolumeType = 'monthly';

        // Function to update the chart based on filter
        function updateVolumeChart(volumeType) {
            const labels = Object.keys(volumeData[volumeType]);
            const data = Object.values(volumeData[volumeType]);

            // Check if data exists
            if (labels.length === 0 || data.length === 0) {
                console.error('No volume data available for the selected period');
                return;
            }

            // Destroy existing chart if it exists to avoid duplication
            if (window.volumeChart && window.volumeChart.destroy) {
                window.volumeChart.destroy();
            }

            // Create new chart instance
            window.volumeChart = new Chart(document.getElementById('volumeChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `${volumeType.charAt(0).toUpperCase() + volumeType.slice(1)} Trade Volume(USDT)`,
                        data: data,
                        backgroundColor: '#4e73df',
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monthly/Weekly Trade Volume(USDT)'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: `${volumeType.charAt(0).toUpperCase() + volumeType.slice(1)} Trade Volume`,
                            font: {
                                size: 24,  // Set the font size for the title
                                weight: 'bold'  // Optionally set the font weight
                            },
                            padding: {
                                top: 20,  // Space between the title and the chart
                                bottom: 30  // Optional, space between the title and chart content
                            }
                        }
                    }
                }
            });

        }

        // Initial chart render
        updateVolumeChart(currentVolumeType);

        // Filter event listener
        document.getElementById('volumeFilter').addEventListener('change', (event) => {
            currentVolumeType = event.target.value;
            updateVolumeChart(currentVolumeType);
        });

    </script>
    // This is for the latest transactions table  
    <script>
        function updateTransactions() {
            fetch('fetch_latest_transactions.php')
                .then(response => response.json())
                .then(data => {
                    const transactionsTableBody = document.querySelector('.table-container-left tbody');
                    transactionsTableBody.innerHTML = ''; // Clear existing rows

                    data.forEach(transaction => {
                        const row = document.createElement('tr');

                        // Check if the 'allow' field is 'off'
                        const isAllowed = transaction.allow === 'off';

                        // Apply a red gradient background if 'allow' is 'off'
                        row.style.background = isAllowed ? 'linear-gradient(to right, #ff7e5f, #feb47b)' : ''; 

                        row.innerHTML = `
                            <td>${transaction.id}</td>
                            <td>${transaction.username}</td>
                            <td>${transaction.symbol}</td>
                            <td>${transaction.order_type.charAt(0).toUpperCase() + transaction.order_type.slice(1)}</td>
                            <td>${parseFloat(transaction.amount).toFixed(6)}</td>
                            <td>${parseFloat(transaction.starting_price).toFixed(6)}</td>
                            <td>${parseFloat(transaction.end_price).toFixed(6)}</td>
                            <td>${parseFloat(transaction.expected_pl).toFixed(2)}</td>
                            <td>${new Date(transaction.created_at).toLocaleString()}</td>
                        `;
                        transactionsTableBody.appendChild(row);
                    });

                    resizeTable(); // Ensure the table layout is applied
                })
                .catch(error => console.error('Error fetching transactions:', error));
        }

        // Function to reapply styles to ensure table is correctly resized
        function resizeTable() {
            const tables = document.querySelectorAll('.table');
            tables.forEach(table => {
                table.style.width = '100%';  // Force table width to 100%
                table.style.tableLayout = 'fixed'; // Apply fixed layout to ensure columns fit
            });
        }

        // Initial fetch and periodic update every 10 seconds
        updateTransactions();
        setInterval(updateTransactions, 10000);
    </script>
<!-- script for logout  -->
<script>
    // Get modal and buttons
const logoutModal = document.getElementById('logoutModal');
const logoutBtn = document.getElementById('logoutBtn');
const confirmLogoutBtn = document.getElementById('confirmLogout');
const cancelLogoutBtn = document.getElementById('cancelLogout');

// Show the modal when the "Logout" button is clicked
logoutBtn.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default link behavior
    logoutModal.style.display = 'block'; // Show the modal
});

// Close the modal when the "Cancel" button is clicked
cancelLogoutBtn.addEventListener('click', function() {
    logoutModal.style.display = 'none'; // Hide the modal
});

// Logout and redirect to admin_login.php when the "OK" button is clicked
confirmLogoutBtn.addEventListener('click', function() {
    window.location.href = 'logout.php'; // Redirect to logout.php
});
</script>


</body>
</html>
