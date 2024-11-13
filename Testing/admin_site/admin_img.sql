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
    usdt DECIMAL(18, 6) DEFAULT 0,
    eth DECIMAL(18, 6) DEFAULT 0,
    btc DECIMAL(18, 6) DEFAULT 0,
    bnd DECIMAL(18, 6) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE coin_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    coin_type ENUM('usdt', 'eth', 'btc', 'bnd'),
    amount DECIMAL(18, 6) NOT NULL,
    image_path VARCHAR(255),
    admin_note TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action ENUM('request', 'approval'),
    coin_type ENUM('usdt', 'eth', 'btc', 'bnd'),
    amount DECIMAL(18, 6),
    admin_note TEXT,
    status ENUM('pending', 'approved', 'rejected'),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `amount` decimal(18,8) NOT NULL,
  `starting_price` decimal(18,6) NOT NULL,
  `end_price` decimal(18,6) NOT NULL,
  `expected_pl` decimal(18,6) NOT NULL,
  `order_type` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
