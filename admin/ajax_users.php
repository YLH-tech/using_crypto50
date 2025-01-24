<?php
session_start();
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


// Ensure only admins can perform actions
if (!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle status update
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        if (isset($_POST['id']) && isset($_POST['status'])) {
            $user_id = $_POST['id'];
            $status = $_POST['status'];

            // Validate status
            if (in_array($status, ['active', 'suspended'])) {
                // Prepare and execute the update statement
                $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
                $stmt->bindValue(':status', $status);
                $stmt->bindValue(':id', $user_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
            }
            exit;
        }
    }
}


// Check if AJAX request contains user_id and allow status
if (isset($_POST['user_id']) && isset($_POST['allow'])) {
    $user_id = $_POST['user_id'];
    $allow = $_POST['allow'];

    // Validate the allow value
    if ($allow !== 'on' && $allow !== 'off') {
        echo json_encode(['success' => false, 'message' => 'Invalid allow value']);
        exit;
    }

    // Update the user's "allow" status
    $stmt = $pdo->prepare("UPDATE users SET allow = :allow WHERE id = :id");
    $stmt->bindValue(':allow', $allow);
    $stmt->bindValue(':id', $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Trade status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update trade status']);
    }
}

// Handle GET requests for user details or fetching user list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If 'user_id' is passed, return user details for the overlay
    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];

        // Validate user_id (assuming it's numeric; adjust as needed)
        if (!is_numeric($user_id)) {
            echo json_encode(['userDetails' => 'Invalid user ID']);
            exit;
        }

        // Query to fetch user details, total orders, total transactions, and total balance
        $stmt = $pdo->prepare("
            SELECT users.*, 
                   COUNT(DISTINCT orders.id) AS total_orders,
                   COUNT(DISTINCT transactions.id) AS total_transactions,
                   user_balances.usdt AS total_balance
            FROM users
            LEFT JOIN orders ON orders.user_id = users.id
            LEFT JOIN transactions ON transactions.user_id = users.id
            LEFT JOIN user_balances ON user_balances.user_id = users.id
            WHERE users.id = :id
            GROUP BY users.id, user_balances.usdt
        ");
        $stmt->bindValue(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Generate HTML for the overlay with user details, orders, transactions, and balance
            $userDetails = "
                <div class='overlay-header'>
                    <span><strong>User ID:</strong> " . htmlspecialchars($user['id']) . "</span><br>
                    <span><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</span><br>
                    <span><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</span><br>
                    <span><strong>Status:</strong> " . htmlspecialchars($user['status']) . "</span><br>
                    <span><strong>Created At:</strong> " . htmlspecialchars($user['created_at']) . "</span><br>
                    <span><strong>Total Orders:</strong> " . htmlspecialchars($user['total_orders']) . "</span><br>
                    <span><strong>Total Transactions:</strong> " . htmlspecialchars($user['total_transactions']) . "</span><br>
                    <span><strong>Total USDT Balance:</strong> " . htmlspecialchars($user['total_balance']) . "</span><br>
                </div>";
    
            echo json_encode(['userDetails' => $userDetails]);
        } else {
            echo json_encode(['userDetails' => 'User not found']);
        }
        exit;
    }

    // Handle fetching user list with filters, pagination, and sorting
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? 'all';
    $sort_order = $_GET['sort_order'] ?? 'newest';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 25; // Users per page
    $offset = ($page - 1) * $limit;

    // Get total user count for pagination
    $totalQuery = "SELECT COUNT(*) FROM users WHERE 1";
    $params = [];

    if ($status_filter != 'all') {
        $totalQuery .= " AND status = :status";
        $params[':status'] = $status_filter;
    }
    if (!empty($search)) {
        $totalQuery .= " AND (id LIKE :search OR username LIKE :search OR email LIKE :search)";
        $params[':search'] = "%$search%";
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
    if (!empty($search)) {
        $query .= " AND (id LIKE :search OR username LIKE :search OR email LIKE :search)";
    }
    $query .= ($sort_order == 'oldest') ? " ORDER BY created_at ASC" : " ORDER BY created_at DESC";
    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    if ($status_filter != 'all') $stmt->bindValue(':status', $status_filter);
    if (!empty($search)) $stmt->bindValue(':search', "%$search%");
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
                    <span class='user-column'>Manage Trade</span> <!-- New Column -->
                    <span class='user-column'>Actions</span>
                  </div>";

    foreach ($users as $user) {
        $userHTML .= "<div class='user-item'>
            <span class='user-column'>" . htmlspecialchars($user['id']) . "</span>
            <span class='user-column'>" . htmlspecialchars($user['username']) . "</span>
            <span class='user-column'>" . htmlspecialchars($user['email']) . "</span>
            <span class='user-column'>" . htmlspecialchars($user['created_at']) . "</span>
            <span class='user-column' id='status-" . htmlspecialchars($user['id']) . "'>" . htmlspecialchars($user['status']) . "</span>
            <span class='user-column'>
                <!-- On/Off Toggle Button -->
                <button class='trade-toggle " . ($user['allow'] == 'on' ? 'on' : 'off') . "' 
                        data-user-id='" . htmlspecialchars($user['id']) . "' 
                        onclick='toggleTrade(this)'>
                    <span class='toggle-text'>" . ($user['allow'] == 'on' ? 'On' : 'Off') . "</span>
                </button>
                <!-- Edit Balances Button -->
                <a href='admin_manage_balance.php?user_id=" . htmlspecialchars($user['id']) . "' class='button'>
                    <i class='fas fa-wallet'></i> Edit Balances
                </a>
            </span>
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

    // Return JSON response
    echo json_encode([
        'userHTML' => $userHTML,
        'paginationHTML' => $paginationHTML
    ]);
}
?>
