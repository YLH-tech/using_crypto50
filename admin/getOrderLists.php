<?php
session_start();
require 'db.php'; // Include the database connection

// Fetch the parameters from the GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$orderType = isset($_GET['order_type']) ? $_GET['order_type'] : '';
$symbol = isset($_GET['symbol']) ? $_GET['symbol'] : '';
$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rowsPerPage = isset($_GET['rows_per_page']) ? $_GET['rows_per_page'] : 25;

// Calculate the offset for pagination
$offset = ($page - 1) * $rowsPerPage;

// Build the SQL query based on filters
$query = "SELECT orders.*, users.username, users.allow FROM orders 
          LEFT JOIN users ON orders.user_id = users.id WHERE 1=1";

// Apply the filters
if ($search) {
    $query .= " AND users.username LIKE :search";  // Search by username instead of user_id
}
if ($orderType) {
    $query .= " AND orders.order_type = :orderType";
}
if ($symbol) {
    $query .= " AND orders.symbol = :symbol";
}
if ($dateRange) {
    switch ($dateRange) {
        case 'today':
            $query .= " AND DATE(orders.created_at) = CURDATE()";
            break;
        case 'last7days':
            $query .= " AND orders.created_at >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last30days':
            $query .= " AND orders.created_at >= CURDATE() - INTERVAL 30 DAY";
            break;
    }
}

// Add sorting by latest created_at first
$query .= " ORDER BY orders.created_at DESC LIMIT :offset, :rowsPerPage";


// Prepare and execute the query
$stmt = $pdo->prepare($query);

if ($search) {
    $stmt->bindValue(':search', "%$search%");
}
if ($orderType) {
    $stmt->bindValue(':orderType', $orderType);
}
if ($symbol) {
    $stmt->bindValue(':symbol', $symbol);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
$stmt->execute();

// Fetch the orders with username
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of rows for pagination
$totalRowsQuery = "SELECT COUNT(*) FROM orders 
                   LEFT JOIN users ON orders.user_id = users.id WHERE 1=1";
if ($search) {
    $totalRowsQuery .= " AND users.username LIKE :search";  // Search by username instead of user_id
}
if ($orderType) {
    $totalRowsQuery .= " AND orders.order_type = :orderType";
}
if ($symbol) {
    $totalRowsQuery .= " AND orders.symbol = :symbol";
}
if ($dateRange) {
    switch ($dateRange) {
        case 'today':
            $totalRowsQuery .= " AND DATE(orders.created_at) = CURDATE()";
            break;
        case 'last7days':
            $totalRowsQuery .= " AND orders.created_at >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'last30days':
            $totalRowsQuery .= " AND orders.created_at >= CURDATE() - INTERVAL 30 DAY";
            break;
    }
}

$totalRowsStmt = $pdo->prepare($totalRowsQuery);
if ($search) {
    $totalRowsStmt->bindValue(':search', "%$search%");
}
if ($orderType) {
    $totalRowsStmt->bindValue(':orderType', $orderType);
}
if ($symbol) {
    $totalRowsStmt->bindValue(':symbol', $symbol);
}
$totalRowsStmt->execute();
$totalRows = $totalRowsStmt->fetchColumn();

// Return the data as JSON
echo json_encode([
    'orders' => $orders,
    'total_rows' => $totalRows,
    'current_page' => $page,
    'rows_per_page' => $rowsPerPage
]);
?>
