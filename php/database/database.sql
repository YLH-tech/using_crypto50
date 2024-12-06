
CREATE DATABASE project;
USE project;

-- for gorgot password
CREATE TABLE `codes` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(5) NOT NULL,
  `expire` int(11) NOT NULL
);

ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `expire` (`expire`),
  ADD KEY `email` (`email`);

  ALTER TABLE `codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  -- for users

  CREATE TABLE `users` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  `username` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `verification_code` TEXT NOT NULL,
  `email_verified_at` datetime NULL,
  `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active' -- Status field with ENUM
);

  CREATE TABLE user_balances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    USDT DECIMAL(20, 6) DEFAULT 0,         -- Tether balance
    BTC DECIMAL(20, 6) DEFAULT 0,          -- Bitcoin balance
    ETH DECIMAL(20, 6) DEFAULT 0,          -- Ethereum balance
    USDC DECIMAL(20, 6) DEFAULT 0,         -- USD Coin balance
    BNB DECIMAL(20, 6) DEFAULT 0,          -- Binance Coin balance
    XRP DECIMAL(20, 6) DEFAULT 0,          -- Ripple (XRP) balance
    DOGE DECIMAL(20, 6) DEFAULT 0,         -- Dogecoin balance
    SOL DECIMAL(20, 6) DEFAULT 0,          -- Solana balance
    ADA DECIMAL(20, 6) DEFAULT 0,          -- Cardano balance
    TRX DECIMAL(20, 6) DEFAULT 0,          -- Tron balance
    DOT DECIMAL(20, 6) DEFAULT 0,          -- Polkadot balance
    LTC DECIMAL(20, 6) DEFAULT 0,          -- Litecoin balance
    BCH DECIMAL(20, 6) DEFAULT 0,          -- Bitcoin Cash balance
    ETC DECIMAL(20, 6) DEFAULT 0,          -- Ethereum Classic balance
    UNI DECIMAL(20, 6) DEFAULT 0,          -- Uniswap balance
    LINK DECIMAL(20, 6) DEFAULT 0,         -- Chainlink balance
    AVAX DECIMAL(20, 6) DEFAULT 0,         -- Avalanche balance
    NEO DECIMAL(20, 6) DEFAULT 0,          -- NEO balance
    EOS DECIMAL(20, 6) DEFAULT 0,          -- EOS balance
    ARB DECIMAL(20, 6) DEFAULT 0,          -- Arbitrum balance
    APT DECIMAL(20, 6) DEFAULT 0,          -- Aptos balance
    TON DECIMAL(20, 6) DEFAULT 0,          -- Toncoin balance
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE coin_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    coin_type ENUM('BTC', 'ETH', 'USDT', 'USDC'),
    amount DECIMAL(18, 6) NOT NULL,
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
    amount DECIMAL(18, 6),
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
    from_amount DECIMAL(18, 6),
    to_amount DECIMAL(18, 6),
    rate DECIMAL(18, 6), -- Exchange rate at the time of transaction
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- withdraw table
CREATE TABLE withdrawal_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    coin VARCHAR(10) NOT NULL,
    amount DECIMAL(16, 6) NOT NULL,
    wallet_address VARCHAR(255) NOT NULL,
    service_charge DECIMAL(16, 6) NOT NULL,
    net_amount DECIMAL(16, 6) NOT NULL,
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
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(255) UNIQUE,
    setting_value TEXT
);
CREATE TABLE orders (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  symbol varchar(20) NOT NULL,
  amount decimal(18,8) NOT NULL,
  starting_price decimal(18,6) NOT NULL,
  end_price decimal(18,6) NOT NULL,
  expected_pl decimal(18,6) NOT NULL,
  order_type varchar(10) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;