<?php
// Transaction.php
require_once 'db.php';

class Transaction {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Record a transaction
    public function recordTransaction($userId, $fromCurrency, $toCurrency, $amount, $convertedAmount) {
        $query = "INSERT INTO transactions (user_id, from_currency, to_currency, amount, converted_amount) 
                  VALUES (:user_id, :from_currency, :to_currency, :amount, :converted_amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':from_currency', $fromCurrency);
        $stmt->bindParam(':to_currency', $toCurrency);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':converted_amount', $convertedAmount);
        return $stmt->execute();
    }

    // Fetch transaction history for a user
    public function getTransactionHistory($userId) {
        $query = "SELECT * FROM transactions WHERE user_id = :user_id ORDER BY timestamp DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
