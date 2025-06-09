-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS atm_db;

-- Use the newly created database
USE atm_db;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    birthday DATE,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Table: transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdraw') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
	current_balance DECIMAL(10, 2) NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);