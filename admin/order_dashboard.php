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

// Coin pairs list
$coinPairs = [
    'BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'DOGEUSDT', 'SOLUSDT', 'ADAUSDT', 'TRXUSDT', 
    'DOTUSDT', 'LTCUSDT', 'BCHUSDT', 'ETCUSDT', 'UNIUSDT', 'LINKUSDT', 'AVAXUSDT', 'NEOUSDT', 
    'EOSUSDT', 'ARBUSDT', 'APTUSDT', 'TONUSDT'
];

// Get the selected time range
$timeRange = isset($_GET['time_range']) ? $_GET['time_range'] : 'today'; // Default to today if not selected

// Calculate the date range based on the selected option
switch ($timeRange) {
    case '7days':
        $dateCondition = "AND orders.created_at >= CURDATE() - INTERVAL 7 DAY";
        break;
    case '30days':
        $dateCondition = "AND orders.created_at >= CURDATE() - INTERVAL 30 DAY";
        break;
    case 'today':
    default:
        $dateCondition = "AND DATE(orders.created_at) = CURDATE()";
        break;
}

// Fetch total orders for each coin pair within the selected date range
$orderCounts = [];
$totalOrders = 0;

foreach ($coinPairs as $coin) {
    $query = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE symbol = ? $dateCondition");
    $query->execute([$coin]);
    $orderCounts[$coin] = $query->fetchColumn();
    $totalOrders += $orderCounts[$coin]; // Add up the total orders
}
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
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="order_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<script src="js/chart.umd.js"></script>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Dashboard</h2>
    </div>
    <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="user_management.php"><i class="fa fa-users"></i> User Management</a></li>
            <li><a href="#"><i class="fa fa-shopping-cart"></i> Orders</a></li>
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
            
            <!-- Logout Button -->
            <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
</aside>

<!-- Main Content -->
<div class="main-content">
    <h1>Order Overview</h1>

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

    <!-- Time Range Filter -->
    <form method="GET" action="order_dashboard.php">
        <label for="time_range">Select Time Range: </label>
        <select name="time_range" id="time_range" onchange="this.form.submit()">
            <option value="today" <?php echo ($timeRange === 'today') ? 'selected' : ''; ?>>Today</option>
            <option value="7days" <?php echo ($timeRange === '7days') ? 'selected' : ''; ?>>Last 7 Days</option>
            <option value="30days" <?php echo ($timeRange === '30days') ? 'selected' : ''; ?>>Last 30 Days</option>
        </select>
    </form>

    <!-- Order Stats Section (Grid) -->
    <div class="order-stats-container">
        <?php foreach ($coinPairs as $coin): ?>
            <div class="order-card" data-symbol="<?php echo $coin; ?>">
                <div class="coin-name"><?php echo $coin; ?></div>
                <div class="order-count"><?php echo $orderCounts[$coin]; ?> Orders</div>
            </div>
        <?php endforeach; ?>

        <div class="order-card total-orders">
            <div class="coin-name">Total Orders</div>
            <div class="order-count"><?php echo $totalOrders; ?> Orders</div>
        </div>

        <!-- Overlay for showing additional details -->
        <div class="overlay" id="orderDetailsOverlay">
            <div class="overlay-content">
                <span class="close-btn" onclick="closeOverlay()">×</span>
                <h2 id="coinName">Coin Name</h2>
                <div class="overlay-info">
                    <p>Total USDT Involved: <span id="totalUSDT">0.000000</span></p>
                    <p>Total Users Traded: <span id="totalUsers">0</span></p>
                    <p>Total Profit: <span id="totalProfit">0.000000</span></p> <!-- Added P/L field -->
                    <p>Total Loss: <span id="totalLoss">0.000000</span></p> <!-- Added P/L field -->
                </div>
            </div>
        </div>

        <!-- Section for Pie Charts -->
        <div class="pie-chart-container">
            <div class="pie-chart">
                <canvas id="buySellChart"></canvas>
                <p>Buy vs Sell Orders</p>
                <p id="buySellNumbers"></p>
            </div>
            <div class="pie-chart">
                <canvas id="profitLossChart"></canvas>
                <p>Profit vs Loss Orders</p>
                <p id="profitLossNumbers"></p>
            </div>
        </div>

    </div>
    <div id="orderListContainer">
    <h2>Order List</h2>
    <div class="filter-container">
        <input type="text" id="searchInput" placeholder="Search by Username" />
        
        <select id="filterOrderType" class="filter">
            <option value="">All Order Types</option>
            <option value="buy">Buy</option>
            <option value="sell">Sell</option>
        </select>

        <select id="filterSymbol" class="filter">
            <option value="">All Symbols</option>
            <option value="BTCUSDT">BTC/USDT</option>
            <option value="ETHUSDT">ETH/USDT</option>
            <option value="BNBUSDT">BNB/USDT</option>
            <option value="XRPUSDT">XRP/USDT</option>
            <option value="DOGEUSDT">DOGE/USDT</option>
            <option value="SOLUSDT">SOL/USDT</option>
            <option value="ADAUSDT">ADA/USDT</option>
            <option value="TRXUSDT">TRX/USDT</option>
            <option value="DOTUSDT">DOT/USDT</option>
            <option value="LTCUSDT">LTC/USDT</option>
            <option value="BCHUSDT">BCH/USDT</option>
            <option value="ETCUSDT">ETC/USDT</option>
            <option value="UNIUSDT">UNI/USDT</option>
            <option value="LINKUSDT">LINK/USDT</option>
            <option value="AVAXUSDT">AVAXUSDT</option>
            <option value="NEOUSDT">NEO/USDT</option>
            <option value="EOSUSDT">EOS/USDT</option>
            <option value="ARBUSDT">ARB/USDT</option>
            <option value="APTUSDT">APT/USDT</option>
            <option value="TONUSDT">TON/USDT</option>
        </select>

        <select id="filterDateRange" class="filter">
            <option value="">All Dates</option>
            <option value="today">Today</option>
            <option value="last7days">Last 7 Days</option>
            <option value="last30days">Last 30 Days</option>
        </select>
        <span id="totalOrdersCount" class="total-orders-count">Total Orders: 0</span>
    </div>
    <table id="orderListTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Symbol</th>
                <th>Order Type</th>
                <th>Amount</th>
                <th>Starting Price</th>
                <th>End Price</th>
                <th>Profit/Loss</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically added here -->
        </tbody>
    </table>
    <div id="pagination" class="pagination-controls"></div>
</div>
<!-- Overlay for user profile (full-screen modal) -->
<div id="userInfoOverlay" class="overlay">
    <div class="overlayContent">
        <div id="userProfileInfo">
            <!-- Profile Info, User Stats, and Balances will be injected here -->
        </div>
        <button id="closeOverlay">Close</button>
    </div>
</div>


<script>
// Set initial page to 1
let currentPage = 1;

// Fetch order list every 5 seconds (initially, will be updated when filters change)
setInterval(() => fetchOrderList(currentPage), 5000);

// Function to fetch order list
function fetchOrderList(page = 1) {
    currentPage = page; // Update the current page when the fetch is triggered

    const search = document.getElementById('searchInput').value.trim();
    const orderType = document.getElementById('filterOrderType').value;
    const symbol = document.getElementById('filterSymbol').value;
    const dateRange = document.getElementById('filterDateRange').value;

    const rowsPerPage = 25; // Adjust as needed

    const params = new URLSearchParams({
        search,
        order_type: orderType,
        symbol,
        date_range: dateRange,
        page,
        rows_per_page: rowsPerPage,
    });

    fetch(`getOrderLists.php?${params.toString()}`)
        .then((response) => response.json())
        .then((data) => {
            const tableBody = document.querySelector('#orderListTable tbody');
            tableBody.innerHTML = ''; // Clear existing rows

            data.orders.forEach((order) => {
                let rowClass = '';  // Default class for row

                // Check if 'allow' is 'off' and apply the red gradient class
                if (order.allow === 'off') {
                    rowClass = 'red-gradient';  // Class for red gradient
                }

                const row = `
                    <tr class="${rowClass}">
                        <td>${order.username}</td> <!-- Show username instead of user_id -->
                        <td>${order.symbol}</td>
                        <td>${order.order_type}</td>
                        <td>${order.amount}</td>
                        <td>${order.starting_price}</td>
                        <td>${order.end_price}</td>
                        <td>${order.expected_pl}</td>
                        <td>${order.created_at}</td>
                        <td><button class="viewUserBtn" data-user-id="${order.user_id}">View User</button></td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });

            updatePagination(data.total_rows, data.current_page, data.rows_per_page);

            const totalOrdersCount = document.getElementById('totalOrdersCount');
            totalOrdersCount.textContent = `Total Orders: ${data.total_rows}`;

            // Attach event listeners to "View User" buttons after fetching new data
            document.querySelectorAll(".viewUserBtn").forEach(button => {
                button.addEventListener("click", function() {
                    const userId = this.getAttribute("data-user-id");
                    showUserProfile(userId);
                });
            });
        })
        .catch((error) => console.error('Error fetching orders:', error));
}

// Function to update pagination
function updatePagination(totalRows, currentPage, rowsPerPage) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = ''; // Clear existing pagination

    const totalPages = Math.ceil(totalRows / rowsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.className = 'pagination-btn'; // Ensure class is added here
        button.classList.toggle('active', i === currentPage); // Toggle active class for current page

        button.addEventListener('click', () => {
            currentPage = i; // Update current page
            fetchOrderList(i); // Trigger fetch with new page
        });

        paginationContainer.appendChild(button);
    }
}

// Trigger the first fetch to load initial data
fetchOrderList(currentPage);

// Add event listeners for filter changes
document.getElementById('searchInput').addEventListener('input', () => fetchOrderList(currentPage));
document.getElementById('filterOrderType').addEventListener('change', () => fetchOrderList(currentPage));
document.getElementById('filterSymbol').addEventListener('change', () => fetchOrderList(currentPage));
document.getElementById('filterDateRange').addEventListener('change', () => fetchOrderList(currentPage));
</script>

<!-- This is for view user button in order list table  -->
<script>
   // Handle "View User" button click
document.querySelectorAll(".viewUserBtn").forEach(button => {
    button.addEventListener("click", function() {
        const userId = this.getAttribute("data-user-id"); // Get the user_id from the button
        console.log(userId); // Check if the ID is being captured
        showUserProfile(userId); // Pass the user_id to the function
    });
});

// Function to display user information in the overlay
function showUserProfile(userId) {
    const overlay = document.getElementById("userInfoOverlay");
    const userInfoContainer = document.getElementById("userProfileInfo");

    // Send AJAX request to fetch user profile data
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'getUserProfile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Ensure user_id is passed as a parameter in the POST request
    xhr.onload = function () {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            
            if (data.error) {
                alert(data.error); // If there's an error, show it
            } else {
                // Display user profile info
                const profile = data.profile;
                const balances = data.balances;
                const ordersMonth = data.orders_month;
                const ordersWeek = data.orders_week;
                
                userInfoContainer.innerHTML = `
                    <div class="userProfileHeader">
                        <img src="${profile.profile_photo}" alt="Profile Photo" class="profilePhoto" />
                        <h3>${profile.username}</h3>
                        <p>User ID: ${userId}</p>
                        <p>Email: ${profile.email}</p>
                    </div>
                    
                    <div class="userStats">
                        <p><strong>Total Traded Orders (Last Month):</strong> ${ordersMonth}</p>
                        <p><strong>Total Traded Orders (Last Week):</strong> ${ordersWeek}</p>
                    </div>

                    <div class="balanceBoxes">
                        <div class="balanceBox">
                            <h4>USDT Balance</h4>
                            <p>${balances.usdt}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>USDC Balance</h4>
                            <p>${balances.usdc}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>BTC Balance</h4>
                            <p>${balances.btc}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>ETH Balance</h4>
                            <p>${balances.eth}</p>
                        </div>
                    </div>
                `;
            }
        } else {
            alert('Error fetching user data.');
        }
    };

    // Pass the user_id to the PHP script as a POST parameter
    xhr.send('user_id=' + encodeURIComponent(userId));

    // Show the overlay
    overlay.style.display = "flex";
}

// Close the overlay
document.getElementById("closeOverlay").addEventListener("click", function() {
    document.getElementById("userInfoOverlay").style.display = "none";
});

</script>

<!-- this is for view user initially define  -->

<script>
    // Handle "View User" button click
document.querySelectorAll(".viewUserBtn").forEach(button => {
    button.addEventListener("click", function() {
        const userId = this.getAttribute("data-user-id"); // Get the user_id from the button
        console.log(userId); // Check if the ID is being captured
        showUserProfile(userId); // Pass the user_id to the function
    });
});

// Function to display user information in the overlay
function showUserProfile(userId) {
    const overlay = document.getElementById("userInfoOverlay");
    const userInfoContainer = document.getElementById("userProfileInfo");

    // Send AJAX request to fetch user profile data
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'getUserProfile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Ensure user_id is passed as a parameter in the POST request
    xhr.onload = function () {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            
            if (data.error) {
                alert(data.error); // If there's an error, show it
            } else {
                // Display user profile info
                const profile = data.profile;
                const balances = data.balances;
                const ordersMonth = data.orders_month;
                const ordersWeek = data.orders_week;
                
                userInfoContainer.innerHTML = `
                    <div class="userProfileHeader">
                        <img src="${profile.profile_photo}" alt="Profile Photo" class="profilePhoto" />
                        <h3>${profile.username}</h3>
                        <p>User ID: ${userId}</p>
                        <p>Email: ${profile.email}</p>
                    </div>
                    
                    <div class="userStats">
                        <p><strong>Total Traded Orders (Within a Month):</strong> ${ordersMonth}</p>
                        <p><strong>Total Traded Orders (Within a Week):</strong> ${ordersWeek}</p>
                    </div>

                    <div class="balanceBoxes">
                        <div class="balanceBox">
                            <h4>USDT Balance</h4>
                            <p>${balances.usdt}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>USDC Balance</h4>
                            <p>${balances.usdc}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>BTC Balance</h4>
                            <p>${balances.btc}</p>
                        </div>
                        <div class="balanceBox">
                            <h4>ETH Balance</h4>
                            <p>${balances.eth}</p>
                        </div>
                    </div>
                `;
            }
        } else {
            alert('Error fetching user data.');
        }
    };

    // Pass the user_id to the PHP script as a POST parameter
    xhr.send('user_id=' + encodeURIComponent(userId));

    // Show the overlay
    overlay.style.display = "flex";
}

// Close the overlay
document.getElementById("closeOverlay").addEventListener("click", function() {
    document.getElementById("userInfoOverlay").style.display = "none";
});

</script>

<!-- This is for pie charts  -->
<script>
function updatePieCharts() {
    const timeRange = document.getElementById('time_range').value;
    fetch(`getOrderPieData.php?time_range=${timeRange}`)
        .then(response => response.json())
        .then(data => {
            // Check if data has the expected properties
            if (data.buyCount !== undefined && data.sellCount !== undefined) {
                // Buy vs Sell chart
                const buySellChart = new Chart(document.getElementById('buySellChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Buy Orders', 'Sell Orders'],
                        datasets: [{
                            data: [data.buyCount, data.sellCount],
                            backgroundColor: ['#4CAF50', '#FF5733'],
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        cutoutPercentage: 60,
                        responsive: true,
                        maintainAspectRatio: true,
                        hover: {
                            animationDuration: 200, // Smooth hover animation
                        },
                        animation: {
                            onHover: function (event, chartElement) {
                                if (chartElement.length) {
                                    this.getDatasetMeta(0).data[chartElement[0].index].scale = 1.1; // Increase size
                                }
                            },
                            onLeave: function (event, chartElement) {
                                if (chartElement.length) {
                                    this.getDatasetMeta(0).data[chartElement[0].index].scale = 1; // Reset size
                                }
                            }
                        },
                        tooltips: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)', // Tooltip background
                            bodyFontSize: 14,
                            xPadding: 10,
                            yPadding: 10,
                        },
                    }
                });
                document.getElementById('buySellNumbers').textContent = `Buy: ${data.buyCount}, Sell: ${data.sellCount}`;

            } else {
                console.error('Invalid data for Buy/Sell chart:', data);
            }

            if (data.profitCount !== undefined && data.lossCount !== undefined) {
                // Profit vs Loss chart
                const profitLossChart = new Chart(document.getElementById('profitLossChart').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Profit Orders', 'Loss Orders'],
                        datasets: [{
                            data: [data.profitCount, data.lossCount],
                            backgroundColor: ['#2196F3', '#FF9800'],
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        cutoutPercentage: 60,
                        responsive: true,
                        maintainAspectRatio: true,
                        hover: {
                            animationDuration: 200, // Smooth hover animation
                        },
                        animation: {
                            onHover: function (event, chartElement) {
                                if (chartElement.length) {
                                    this.getDatasetMeta(0).data[chartElement[0].index].scale = 1.1; // Increase size
                                }
                            },
                            onLeave: function (event, chartElement) {
                                if (chartElement.length) {
                                    this.getDatasetMeta(0).data[chartElement[0].index].scale = 1; // Reset size
                                }
                            }
                        },
                        tooltips: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)', // Tooltip background
                            bodyFontSize: 14,
                            xPadding: 10,
                            yPadding: 10,
                        },
                    }
                });
                document.getElementById('profitLossNumbers').textContent = `Profit: ${data.profitCount}, Loss: ${data.lossCount}`;
            } else {
                console.error('Invalid data for Profit/Loss chart:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching pie chart data:', error);
        });
}


// Update pie charts when time range changes
document.getElementById('time_range').addEventListener('change', updatePieCharts);

// Initial load for pie charts
updatePieCharts();
</script>

<script>
    // JavaScript to handle card clicks
    document.querySelectorAll('.order-card').forEach(card => {
        card.addEventListener('click', function() {
            const symbol = this.getAttribute('data-symbol') || ''; // Use empty string for Total Orders
            const timeRange = document.getElementById('time_range').value; // Get the selected time range
            fetchOrderDetails(symbol, timeRange);
        });
    });

    function fetchOrderDetails(symbol, timeRange) {
        // Send both the symbol and time range to the backend
        fetch(`getOrderDetails.php?symbol=${symbol}&time_range=${timeRange}`)
            .then(response => response.json())
            .then(data => {
                // Display order details in the overlay
                document.getElementById('coinName').textContent = symbol ? symbol : 'Total Orders';
                document.getElementById('totalUSDT').textContent = parseFloat(data.totalUSDT).toFixed(6);
                document.getElementById('totalUsers').textContent = data.totalUsers;
                document.getElementById('totalProfit').textContent = parseFloat(data.totalProfit).toFixed(6); // Display Total Profit
                document.getElementById('totalLoss').textContent = parseFloat(data.totalLoss).toFixed(6); // Display Total Loss

                // Show overlay
                document.getElementById('orderDetailsOverlay').style.display = 'flex';
            })
            .catch(error => console.error('Error fetching data:', error));
    }




    function closeOverlay() {
        document.getElementById('orderDetailsOverlay').style.display = 'none';
    }



    // JavaScript for real-time fetching of order stats
    function fetchOrderStats() {
        fetch('getOrderStats.php')  // Assuming you have a backend endpoint for real-time order stats
            .then(response => response.json())
            .then(data => {
                // Update individual coin stats dynamically based on data from the backend
                Object.entries(data.orderCounts).forEach(([symbol, count]) => {
                    const orderCard = document.querySelector(`.order-card[data-symbol="${symbol}"]`);
                    if (orderCard) {
                        orderCard.querySelector('.order-count').textContent = `${count} Orders`;
                    }
                });

                // Update total orders (Total Orders Card)
                document.querySelector('.total-orders .order-count').textContent = `${data.totalOrders} Orders`;

                // Update total USDT for Total Orders card
                document.getElementById('totalUSDT').textContent = parseFloat(data.totalUSDT).toFixed(6);
                document.getElementById('totalUsers').textContent = data.totalUsers;
            })
            .catch(error => console.error('Error fetching order stats:', error));
    }

    // Fetch stats every 10 seconds for real-time update
    setInterval(fetchOrderStats, 10000);

    // Initial fetch for stats on page load
    fetchOrderStats();

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
