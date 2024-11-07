CREATE DATABASE admin_page;

USE admin_page;


CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_balances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    BTC DECIMAL(20, 4) DEFAULT 0,          -- Bitcoin balance
    ETH DECIMAL(20, 4) DEFAULT 0,          -- Ethereum balance
    USDT DECIMAL(20, 4) DEFAULT 0,         -- Tether balance
    USDC DECIMAL(20, 4) DEFAULT 0,         -- USD Coin balance
    BND DECIMAL(20, 4) DEFAULT 0,          -- Binance Coin balance
    DOGE DECIMAL(20, 4) DEFAULT 0,         -- Dogecoin balance
    TRX DECIMAL(20, 4) DEFAULT 0,          -- Tron balance
    DOT DECIMAL(20, 4) DEFAULT 0,          -- Polkadot balance
    ADA DECIMAL(20, 4) DEFAULT 0,          -- Cardano balance
    BSV DECIMAL(20, 4) DEFAULT 0,          -- Bitcoin SV balance
    XRP DECIMAL(20, 4) DEFAULT 0,          -- Ripple (XRP) balance
    LTC DECIMAL(20, 4) DEFAULT 0,          -- Litecoin balance
    EOS DECIMAL(20, 4) DEFAULT 0,          -- EOS balance
    BCH DECIMAL(20, 4) DEFAULT 0,          -- Bitcoin Cash balance
    DF DECIMAL(20, 4) DEFAULT 0,           -- Decentralized Finance balance (replace with actual coin if needed)
    QTUM DECIMAL(20, 4) DEFAULT 0,         -- Qtum balance
    IOTA DECIMAL(20, 4) DEFAULT 0,         -- IOTA balance
    NEO DECIMAL(20, 4) DEFAULT 0,          -- NEO balance
    NAS DECIMAL(20, 4) DEFAULT 0,          -- NAS balance
    ELA DECIMAL(20, 4) DEFAULT 0,          -- Elastos balance
    SNT DECIMAL(20, 4) DEFAULT 0,          -- Status balance
    WICC DECIMAL(20, 4) DEFAULT 0,          -- WICC balance
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE coin_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    coin_type ENUM('BTC', 'ETH', 'USDT', 'USDC'),
    amount DECIMAL(18, 4) NOT NULL,
    image_path VARCHAR(255),
    admin_note TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- exchange table
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action ENUM('request', 'approval'),
    coin_type ENUM('BTC', 'ETH', 'USDT', 'USDC'),
    amount DECIMAL(18, 4),
    admin_note TEXT,
    hidden_admin TINYINT(1) DEFAULT 0,
    hidden_user TINYINT(1) DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected'),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE `transactions_exchange` (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    from_coin VARCHAR(10),
    to_coin VARCHAR(10),
    from_amount DECIMAL(18, 4),
    to_amount DECIMAL(18, 4),
    rate DECIMAL(18, 6), -- Exchange rate at the time of transaction
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- withdraw table
CREATE TABLE withdrawal_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    coin VARCHAR(10) NOT NULL,
    amount DECIMAL(16, 4) NOT NULL,
    wallet_address VARCHAR(255) NOT NULL,
    service_charge DECIMAL(16, 4) NOT NULL,
    net_amount DECIMAL(16, 4) NOT NULL,
    hidden_admin TINYINT(1) DEFAULT 0,
    hidden_user TINYINT(1) DEFAULT 0,
    status ENUM('Waiting a few movement', 'Approved and Please check your Wallet', 'Rejected and Server Maintaining') DEFAULT 'Waiting a few movement',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE fund_passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fund_password VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- order table
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `amount` decimal(18,8) NOT NULL,
  `starting_price` decimal(18,2) NOT NULL,
  `end_price` decimal(18,2) NOT NULL,
  `expected_pl` decimal(18,6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
