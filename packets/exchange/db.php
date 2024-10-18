<?php
// db.php
class Database {
    private $host = 'localhost';
    private $db_name = 'crypto_exchange';
    private $username = 'root'; // Change to your DB username
    private $password = ''; // Change to your DB password
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
