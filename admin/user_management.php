<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';
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


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Initialize filter variables
$sort_order = $_GET['sort_order'] ?? 'oldest';
$status_filter = $_GET['status'] ?? 'all';
$search_keyword = $_GET['search'] ?? '';

// Pagination setup
$limit = 25; // Users per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total user count for pagination
$totalQuery = "SELECT COUNT(*) FROM users WHERE 1";
$params = [];

if ($status_filter != 'all') {
    $totalQuery .= " AND status = :status";
    $params[':status'] = $status_filter;
}
if (!empty($search_keyword)) {
    $totalQuery .= " AND (id LIKE :search OR username LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search_keyword%";
}
 
$totalStmt = $pdo->prepare($totalQuery);
foreach ($params as $key => $value) {
    $totalStmt->bindValue($key, $value);
}
$totalStmt->execute();
$total_users = $totalStmt->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Build query with filters
$query = "SELECT * FROM users WHERE 1";
if ($status_filter != 'all') {
    $query .= " AND status = :status";
}
if (!empty($search_keyword)) {
    $query .= " AND (id LIKE :search OR username LIKE :search OR email LIKE :search)";
}
$query .= ($sort_order == 'oldest') ? " ORDER BY created_at ASC" : " ORDER BY created_at DESC";
$query .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
if ($status_filter != 'all') $stmt->bindValue(':status', $status_filter);
if (!empty($search_keyword)) $stmt->bindValue(':search', "%$search_keyword%");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Build HTML for user list with titles
$userHTML = "<div class='user-header'>
                <span class='user-column'>User ID</span>
                <span class='user-column'>Username</span>
                <span class='user-column'>Email</span>
                <span class='user-column'>Created At</span>
                <span class='user-column'>Status</span>
                <span class='user-column'>View</span>
                <span class='user-column'>Actions</span>
              </div>";

foreach ($users as $user) {
    $userHTML .= "<div class='user-item'>
                    <span class='user-column'>" . htmlspecialchars($user['id']) . "</span>
                    <span class='user-column'>" . htmlspecialchars($user['username']) . "</span>
                    <span class='user-column'>" . htmlspecialchars($user['email']) . "</span>
                    <span class='user-column'>" . htmlspecialchars($user['created_at']) . "</span>
                    <span class='user-column' id='status-" . htmlspecialchars($user['id']) . "'>" . htmlspecialchars($user['status']) . "</span>
                    <a href='javascript:void(0)' class='button' onclick='showUserOverlay(\"" . htmlspecialchars($user['id']) . "\")'>
                        <i class='fas fa-eye'></i> View
                    </a>
                    <a href='javascript:void(0)' class='button' onclick='showActionOptions(\"" . htmlspecialchars($user['id']) . "\", \"" . htmlspecialchars($user['status']) . "\")'>
                        <i class='fas fa-cog'></i> Action
                    </a>
                  </div>";
}

// Build HTML for pagination
$paginationHTML = '';
if ($page > 1) {
    $paginationHTML .= "<a href='javascript:void(0)' onclick='filterUsers(" . ($page - 1) . ")' class='back-next'>Back</a>";
}

for ($i = 1; $i <= $total_pages; $i++) {
    $paginationHTML .= "<a href='javascript:void(0)' onclick='filterUsers($i)' " . ($page == $i ? 'class="active"' : '') . ">$i</a>";
}

if ($page < $total_pages) {
    $paginationHTML .= "<a href='javascript:void(0)' onclick='filterUsers(" . ($page + 1) . ")' class='back-next'>Next</a>";
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
    <title>User Management</title>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="user_management.css">
    <style>
        /* Basic styles for overlays */
        #userOverlay, #actionOverlay {
            display: none;
            position: fixed;
            top: 0;
            left:0;
            width:100%;
            height:100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #userOverlay div, #actionOverlay div {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            width: 90%;
            max-height: 80%;
            overflow-y: auto;
        }

        /* Optional: Style the buttons inside the overlays */
        #actionOverlay button {
            margin: 5px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<script>
        function toggleSidebar() {
            // Toggle the sidebar active state
            document.querySelector('.sidebar').classList.toggle('active');
            
            // Toggle the hamburger active state to change color
            document.querySelector('.hamburger').classList.toggle('active');
        }
    </script>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#"><i class="fa fa-users"></i> User Management</a></li>
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
            

            <!-- Logout Button -->
            <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header-wrapper">
                <div class="hamburger">
                    <i class="fas fa-bars"></i>
                </div>
                <h2>User Management</h2>
            </div>
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
        <!-- Filters and Search Form -->
        <form method="get" class="filter-form" id="filter-form">
            <div class="left-filters">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="user_id (or) name (or) email" onkeyup="filterUsers()" value="<?= htmlspecialchars($search_keyword) ?>">
            </div>
            
            <div class="right-filters">
                <label for="status">Status:</label>
                <select id="status" name="status" onchange="filterUsers()">
                    <option value="all">All</option>
                    <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= ($status_filter == 'suspended') ? 'selected' : '' ?>>Suspended</option>
                </select>

                <label for="sort_order">Sort by Date:</label>
                <select id="sort_order" name="sort_order" onchange="filterUsers()">
                    <option value="newest" <?= ($sort_order == 'newest') ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest" <?= ($sort_order == 'oldest') ? 'selected' : '' ?>>Oldest</option>
                </select>
            </div>
        </form>


        <!-- User List with Titles -->
        <div class="user-list">
            <div class="user-header">
                <span class="user-column">User ID</span>
                <span class="user-column">Username</span>
                <span class="user-column">Email</span>
                <span class="user-column">Created At</span>
                <span class="user-column">Status</span>
                <span class="user-column">View</span>
                <span class="user-column">Actions</span>
            </div>
            <?php foreach ($users as $user): ?>
                <div class="user-item">
                    <span class="user-column"><?= htmlspecialchars($user['user_id'] ?? '') ?></span>
                    <span class="user-column"><?= htmlspecialchars($user['username'] ?? '') ?></span>
                    <span class="user-column"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                    <span class="user-column"><?= htmlspecialchars($user['created_at'] ?? '') ?></span>
                    <span class="user-column" id="status-<?= htmlspecialchars($user['user_id']) ?>"><?= htmlspecialchars($user['status'] ?? '') ?></span>
                    <!-- The View button now triggers AJAX for the overlay -->
                    <a href="javascript:void(0)" class="button" onclick="showUserOverlay('<?= htmlspecialchars($user['user_id']) ?>')">
                        <i class="fas fa-eye"></i> View
                    </a>

                    <a href="javascript:void(0)" class="button" onclick="showActionOptions('<?= htmlspecialchars($user['user_id']) ?>', '<?= htmlspecialchars($user['status']) ?>')">
                        <i class="fas fa-cog"></i> Action
                    </a>                
                </div>
            <?php endforeach; ?>
        </div>

        <!-- For View button  -->
        <div id="userOverlay">
            <div id="overlayContent"></div>
        </div>

        <!-- Action Options Overlay -->
        <div id="actionOverlay">
            <div>
                <h3>Change User Status</h3>
                <p>Select the desired action for this user:</p>
                <button onclick="updateUserStatus(selectedUserId, 'active')">Activate</button>
                <button onclick="updateUserStatus(selectedUserId, 'suspended')">Suspend</button>
                <button onclick="closeActionOverlay()">Cancel</button>
            </div>
        </div>


        <!-- Pagination Links with Back and Next Buttons -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="javascript:void(0)" onclick="filterUsers(<?= $page - 1 ?>)" class="back-next">Back</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="javascript:void(0)" onclick="filterUsers(<?= $i ?>)" <?= ($page == $i ? 'class="active"' : '') ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="javascript:void(0)" onclick="filterUsers(<?= $page + 1 ?>)" class="back-next">Next</a>
            <?php endif; ?>
        </div>
    </div>
</div>


    <!-- This is for on/off button in 'Manage Trade' column -->
    <script>
        function toggleTrade(buttonElement) {
            var userId = buttonElement.getAttribute('data-user-id');
            var isOn = buttonElement.classList.contains('on');

            // Show the animation (toggle class)
            buttonElement.classList.add('transition');
            setTimeout(function() {
                buttonElement.classList.remove('transition');
                if (isOn) {
                    buttonElement.classList.remove('on');
                    buttonElement.classList.add('off');
                    buttonElement.querySelector('.toggle-text').textContent = 'Off';
                } else {
                    buttonElement.classList.remove('off');
                    buttonElement.classList.add('on');
                    buttonElement.querySelector('.toggle-text').textContent = 'On';
                }
            }, 300);  // Match with the CSS transition duration

            // Make AJAX call to update the database
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_users.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Handle successful response, if needed
                }
            };
            xhr.send('user_id=' + userId + '&allow=' + (isOn ? 'off' : 'on'));
        }


    </script>


    <script>
        let selectedUserId = null;

        // Function to open the Action Options Overlay
        function showActionOptions(userId, currentStatus) {
            selectedUserId = userId;
            document.getElementById('actionOverlay').style.display = 'flex';
        }

        // Function to close the Action Options Overlay
        function closeActionOverlay() {
            selectedUserId = null;
            document.getElementById('actionOverlay').style.display = 'none';
        }

        // Function to update user status via AJAX
        function updateUserStatus(userId, status) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_users.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Update the status in the UI without reloading
                            document.getElementById('status-' + userId).innerText = status;
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    } catch (e) {
                        alert('An error occurred while processing the response.');
                    }
                    closeActionOverlay();
                }
            };
            
            xhr.send(`action=update_status&id=${encodeURIComponent(userId)}&status=${encodeURIComponent(status)}`);
        }

        // Function to open the user overlay and fetch user details via AJAX
        function showUserOverlay(userId) {
            const overlay = document.getElementById('userOverlay');
            const overlayContent = document.getElementById('overlayContent');

            // Send AJAX request to get user details
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'ajax_users.php?id=' + encodeURIComponent(userId), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.userDetails) {
                            // Populate the overlay with the user details returned from the server
                            overlayContent.innerHTML = response.userDetails;
                            overlay.style.display = 'flex'; // Ensure the overlay is centered and visible
                        } else {
                            overlayContent.innerHTML = 'User details could not be retrieved.';
                            overlay.style.display = 'flex';
                        }
                    } catch (e) {
                        overlayContent.innerHTML = 'An error occurred while processing the response.';
                        overlay.style.display = 'flex';
                    }
                }
            };
            xhr.send();
        }

        // Function to close the user overlay
        function closeOverlay() {
            document.getElementById('userOverlay').style.display = 'none'; // Hide overlay
        }

        // Close overlay if clicked outside of it
        document.getElementById('userOverlay').addEventListener('click', function(event) {
            if (event.target === this) {
                closeOverlay();
            }
        });

        // Function for search and filter
        function filterUsers(page = 1) {
        const search = document.getElementById('search').value;
        const status = document.getElementById('status').value;
        const sort_order = document.getElementById('sort_order').value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `ajax_users.php?page=${encodeURIComponent(page)}&search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}&sort_order=${encodeURIComponent(sort_order)}`, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.userHTML && response.paginationHTML) {
                        document.querySelector('.user-list').innerHTML = response.userHTML;
                        document.querySelector('.pagination').innerHTML = response.paginationHTML;
                    } else {
                        // Log error to the console instead of showing an alert
                        console.error('Unexpected response structure:', response);
                    }
                } catch (e) {
                    // Log the parsing error to the console
                    console.error('Error parsing response:', e);
                }
            } else {
                // Log the request failure instead of showing an alert
                console.error('Failed to fetch user data. Status:', xhr.status);
            }
        };
        xhr.send();
    }


        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            filterUsers();
        });
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
