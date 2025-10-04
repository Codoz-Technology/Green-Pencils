-- Create database
CREATE DATABASE IF NOT EXISTS siva_polybag;
USE siva_polybag;

-- Create custom_orders table
CREATE TABLE IF NOT EXISTS custom_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    phone VARCHAR(20),
    bag_types VARCHAR(255),
    width DECIMAL(10,2),
    height DECIMAL(10,2),
    thickness DECIMAL(10,2),
    quantity VARCHAR(50),
    requirements TEXT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending'
); 