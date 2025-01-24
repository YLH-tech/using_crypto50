
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
require 'db_connection.php';

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

// Fetch current support links from the database
$telegramLink = '';
$helpCenterLink = '';

$query = $pdo->prepare("SELECT setting_name, setting_value FROM settings WHERE setting_name IN ('telegram_link', 'help_center_link')");
$query->execute();
$settings = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($settings as $setting) {
    if ($setting['setting_name'] === 'telegram_link') {
        $telegramLink = $setting['setting_value'];
    } elseif ($setting['setting_name'] === 'help_center_link') {
        $helpCenterLink = $setting['setting_value'];
    }
}

// Handle support links update form submission
if (isset($_POST['verify_code'])) {
    $newTelegramLink = trim($_POST['telegram_link']);
    $newHelpCenterLink = trim($_POST['help_center_link']);
    $adminCode = trim($_POST['admin_code']); // Retrieve the admin code submitted from the modal

    // Validate the admin code
    $query = $pdo->prepare("SELECT clcode FROM admin_users WHERE clcode = ?");
    $query->execute([$adminCode]);
    $storedCode = $query->fetch(PDO::FETCH_ASSOC);

    if ($storedCode) {
        // If the admin code is valid, proceed with saving the settings

        // Update Telegram link if it's not empty
        if (!empty($newTelegramLink)) {
            $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES ('telegram_link', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = ?")->execute([$newTelegramLink, $newTelegramLink]);
        }

        // Update Help Center link if it's not empty
        if (!empty($newHelpCenterLink)) {
            $pdo->prepare("INSERT INTO settings (setting_name, setting_value) VALUES ('help_center_link', ?) 
                          ON DUPLICATE KEY UPDATE setting_value = ?")->execute([$newHelpCenterLink, $newHelpCenterLink]);
        }

        // Set a success message if any change was made
        if (!empty($newTelegramLink) || !empty($newHelpCenterLink)) {
            $_SESSION['success_message'] = "Changes saved successfully!";
        } else {
            $_SESSION['success_message'] = "No changes were made. Please fill at least one field.";
        }
    } else {
        // If the admin code is invalid, set an error message
        $_SESSION['error_message'] = "Invalid admin code entered. Changes were not saved.";
    }

    // Redirect to prevent form resubmission
    header("Location: #");
    exit;
}

// Initialize the notification visibility flag
$showNotification = isset($_SESSION['success_message']);

// Fetch the count of pending coin requests
$pending_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM coin_requests WHERE status = 'pending'");
$pending_requests_count = $pending_requests_count_stmt->fetch()['count'];

// Fetch the count of pending coin requests
$waiting_requests_count_stmt = $pdo->query("SELECT COUNT(*) as count FROM withdrawal_requests WHERE status = 'Waiting a few movement'");
$waiting_requests_count = $waiting_requests_count_stmt->fetch()['count'];

// Handle password change request form submission
$errorMessage = '';
if (isset($_POST['change_password'])) {
    $adminCode = trim($_POST['admin_code']);

    // Fetch the stored admin codes from the database
    $query = $pdo->prepare("SELECT cpcode FROM admin_users WHERE cpcode = ?");
    $query->execute([$adminCode]);
    $storedCode = $query->fetch(PDO::FETCH_ASSOC);

    if ($storedCode) {
        // If the code matches, redirect to the change password page
        header('Location: forgot.php?mode=enter_password');
        exit;
    } else {
        $errorMessage = 'Invalid code entered. Please try again.';
    }
}
if (!empty($errorMessage)) {
    $_SESSION['error_message'] = $errorMessage;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="settings.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Notification Box for Success and Error -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="notification success" id="notificationBox">
            <?php echo $_SESSION['success_message']; ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="notification error" id="notificationBox">
            <?php echo $_SESSION['error_message']; ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>


    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
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
        <h1>Admin Settings</h1>
        <!-- Modal for Admin Code Verification -->
        <div id="adminCodeModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2>Enter Code to Save Changes</h2>
                <form id="adminCodeForm" method="POST">
                    <input type="hidden" name="verify_code" value="1">
                    <div class="form-group">
                        <label for="admin_code_modal">Admin Code:</label>
                        <input type="text" id="admin_code_modal" name="admin_code" placeholder="Enter 10-character code" maxlength="10" required autocomplete="off">
                    </div>
                    <button type="button" id="cancelButton">Cancel</button>
                    <button type="submit">OK</button>
                </form>
            </div>
        </div>

        
        <fieldset>
            <legend>Update Support Links</legend>
            <!-- Form for updating support links -->
            <form id="mainSettingsForm">
                <input type="hidden" name="save_settings" value="1">
                <div class="form-group">
                    <label for="telegram_link">Telegram Account Link:</label>
                    <input type="url" id="telegram_link" name="telegram_link" value="" 
                        placeholder="e.g. https://t.me/yourchannel" autocomplete="off">
                    <a href="<?php echo htmlspecialchars($telegramLink) ?: '#'; ?>" target="_blank">
                        Current: <?php echo htmlspecialchars($telegramLink) ?: 'Not Set'; ?>
                    </a>
                </div>

                <div class="form-group">
                    <label for="help_center_link">Help Center Link:</label>
                    <input type="url" id="help_center_link" name="help_center_link" value=""
                        placeholder="e.g. https://yourwebsite.com/help" autocomplete="off">
                    <a href="<?php echo htmlspecialchars($helpCenterLink) ?: '#'; ?>" target="_blank">
                        Current: <?php echo htmlspecialchars($helpCenterLink) ?: 'Not Set'; ?>
                    </a>
                </div>

                <button type="button" id="saveChangesButton">Save Changes</button>
            </form>
        </fieldset>
        <fieldset>
            <legend>Change Admin Password</legend>
            <!-- Form for changing admin password -->
            <form method="POST">
                <div class="form-group3">
                    <input type="hidden" name="change_password" value="1">
                    <label for="admin_code">Enter Code:</label>
                    <input type="text" id="admin_code" name="admin_code" placeholder="Enter 10-character code" maxlength="10" required autocomplete="off">

                </div>
                <button type="submit">Submit</button>
            </form>
        </fieldset>
    </div>

    

    <script>
        // Show the notification dynamically
        document.addEventListener('DOMContentLoaded', function() {
            var notification = document.getElementById('notificationBox');
            if (notification) {
                notification.classList.add('show'); // Show the notification
                setTimeout(function() {
                    notification.classList.remove('show'); // Hide the notification after 5 seconds
                }, 5000); // Adjust timing as needed
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const saveChangesButton = document.getElementById("saveChangesButton");
            const adminCodeModal = document.getElementById("adminCodeModal");
            const closeModal = document.getElementById("closeModal");
            const cancelButton = document.getElementById("cancelButton");
            const adminCodeForm = document.getElementById("adminCodeForm");

            // Show modal when Save Changes button is clicked
            saveChangesButton.addEventListener("click", () => {
                const telegramLink = document.getElementById("telegram_link").value.trim();
                const helpCenterLink = document.getElementById("help_center_link").value.trim();

                if (!telegramLink && !helpCenterLink) {
                    // Show a notification for no changes made
                    showNotification("No changes were made. Please fill at least one field.", "error");
                    return; // Do not proceed to open the modal
                }

                // If at least one field is filled, show the modal
                adminCodeModal.style.display = "block";
            });

            closeModal.addEventListener("click", () => {
                adminCodeModal.style.display = "none";
            });

            cancelButton.addEventListener("click", () => {
                adminCodeModal.style.display = "none";
            });

            adminCodeForm.addEventListener("submit", (e) => {
                e.preventDefault();

                const telegramLink = document.getElementById("telegram_link").value;
                const helpCenterLink = document.getElementById("help_center_link").value;

                const telegramInput = document.createElement("input");
                telegramInput.type = "hidden";
                telegramInput.name = "telegram_link";
                telegramInput.value = telegramLink;
                adminCodeForm.appendChild(telegramInput);

                const helpCenterInput = document.createElement("input");
                helpCenterInput.type = "hidden";
                helpCenterInput.name = "help_center_link";
                helpCenterInput.value = helpCenterLink;
                adminCodeForm.appendChild(helpCenterInput);

                adminCodeForm.submit();
            });

            function showNotification(message, type) {
                const notification = document.createElement("div");
                notification.className = `notification ${type}`;
                notification.textContent = message;

                // Append the notification to the body or a specific container
                document.body.appendChild(notification);

                // Add the show class to display it
                notification.classList.add("show");

                // Remove the notification after 5 seconds
                setTimeout(() => {
                    notification.classList.remove("show");
                    notification.remove();
                }, 5000);
            }
        });

    </script>
<!-- script for logout  -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
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

        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });
    });

</script>
</body>
</html>
