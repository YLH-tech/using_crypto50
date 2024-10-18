CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    balance_usdt DECIMAL(12, 2) DEFAULT 0, -- User's balance in USDT
    balance_btc DECIMAL(12, 8) DEFAULT 0,  -- User's balance in BTC
    balance_eth DECIMAL(12, 8) DEFAULT 0   -- User's balance in ETH
);

-- Orders table to store buy/sell orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_type VARCHAR(4) NOT NULL, -- 'Buy' or 'Sell'
    trading_pair VARCHAR(10) NOT NULL, -- e.g., 'BTC/USDT'
    amount DECIMAL(12, 8) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    fee DECIMAL(12, 2) NOT NULL,  -- Transaction fee
    status VARCHAR(20) DEFAULT 'pending', -- Status of the order: pending, completed, canceled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);